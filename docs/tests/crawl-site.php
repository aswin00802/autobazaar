<?php
/**
 * Walks the whole customer website and reports anything broken.
 *
 * It starts from sitemap.xml, opens every page, then follows every internal
 * link and image it finds, and checks:
 *   - the page opens (no error page)
 *   - no PHP warning or leftover template code is visible
 *   - the SEO tags are present (title, description, canonical, share image, one H1)
 *   - the Google structured data is readable
 *   - no broken links and no missing images
 *   - no two pages share the same title
 *
 * Run it from a command window in the project folder:
 *     php docs/tests/crawl-site.php
 *
 * If your website address is different, change $base below.
 */
$base = 'http://localhost/autobazaar/public';

set_time_limit(0);

function fetch($url, $headOnly = false) {
    $c = curl_init($url);
    curl_setopt_array($c, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 90, CURLOPT_FOLLOWLOCATION => 0, CURLOPT_NOBODY => $headOnly]);
    $body = curl_exec($c);
    $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $location = curl_getinfo($c, CURLINFO_REDIRECT_URL);
    curl_close($c);

    return [$status, (string) $body, $location];
}
function tag($html, $pattern) { return preg_match($pattern, $html, $m) ? html_entity_decode($m[1], ENT_QUOTES) : null; }

[, $xml] = fetch("$base/sitemap.xml");
$sitemap = simplexml_load_string($xml);

$queue = [];
foreach ($sitemap->url ?? [] as $u) { $queue[(string) $u->loc] = true; }
// pages the sitemap leaves out on purpose (private or thin), still worth checking
foreach (['/cart', '/search?q=tvs', '/enquiry', '/compare/tvs-king-deluxe-vs-bajaj-re', '/user/login', '/user/register', '/account', '/checkout'] as $p) {
    $queue[$base . $p] = true;
}

$pages = $links = $images = [];

foreach (array_keys($queue) as $url) {
    [$status, $html, $location] = fetch($url);
    $path = str_replace($base, '', $url) ?: '/';

    if ($status >= 300 && $status < 400) {
        $pages[$path] = ['status' => $status, 'issues' => ['redirects to ' . str_replace($base, '', (string) $location)], 'seo' => '-'];
        continue;
    }

    $issues = [];
    if ($status >= 400) { $issues[] = "page does not open (HTTP $status)"; }
    foreach (['Warning</b>', 'Notice</b>', 'Fatal error', 'Undefined variable', 'Undefined array key', 'Stack trace', 'SQLSTATE', '&amp;amp;'] as $needle) {
        if (stripos($html, $needle) !== false) { $issues[] = "shows '$needle'"; }
    }

    $title = tag($html, '/<title>([^<]*)<\/title>/');
    $description = tag($html, '/<meta name="description" content="([^"]*)"/');
    $seo = [];
    if (! $title) { $seo[] = 'no title'; } elseif (mb_strlen($title) > 70) { $seo[] = 'title is long (' . mb_strlen($title) . ')'; }
    if (! $description) { $seo[] = 'no description'; } elseif (mb_strlen($description) < 70) { $seo[] = 'description is short (' . mb_strlen($description) . ')'; }
    if (! tag($html, '/<link rel="canonical" href="([^"]*)"/')) { $seo[] = 'no canonical link'; }
    if (! tag($html, '/<meta property="og:image" content="([^"]*)"/')) { $seo[] = 'no share image'; }
    $h1 = preg_match_all('/<h1\b/', $html);
    if ($h1 !== 1) { $seo[] = "main heading count is $h1"; }
    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $ld);
    foreach ($ld[1] as $json) { if (! is_array(json_decode($json, true))) { $seo[] = 'broken Google data'; break; } }
    $noAlt = preg_match_all('/<img(?![^>]*\balt=)[^>]*>/', $html);
    if ($noAlt) { $seo[] = "$noAlt images with no description"; }

    $pages[$path] = ['status' => $status, 'issues' => $issues, 'seo' => $seo ? implode('; ', $seo) : 'ok', 'title' => $title];

    preg_match_all('/<a\s[^>]*href="([^"#]+)"/', $html, $a);
    foreach ($a[1] as $href) { $href = html_entity_decode($href); if (str_starts_with($href, $base)) { $links[strtok($href, '#')][] = $path; } }
    preg_match_all('/<img\s[^>]*src="([^"]+)"/', $html, $im);
    foreach ($im[1] as $src) { $src = html_entity_decode($src); if (str_starts_with($src, $base)) { $images[$src][] = $path; } }
}

echo "=== PAGES CHECKED: " . count($pages) . "\n";
$flagged = 0;
foreach ($pages as $path => $p) {
    if ($p['issues'] || ($p['seo'] !== 'ok' && $p['seo'] !== '-')) {
        $flagged++;
        printf("%-52s %s | %s | SEO: %s\n", substr($path, 0, 52), $p['status'], $p['issues'] ? implode(', ', $p['issues']) : 'opens fine', $p['seo']);
    }
}
if (! $flagged) { echo "every page opens and passes the SEO checks\n"; }

$todo = array_filter(array_diff(array_keys($links), array_keys($queue)), fn ($u) => ! preg_match('#/(cart/|logout|user/otp|login/)#', $u));
echo "\n=== OTHER LINKS CHECKED: " . count($todo) . "\n";
$broken = 0;
foreach ($todo as $u) {
    [$st] = fetch($u);
    if ($st >= 400) { $broken++; echo "BROKEN $st  " . str_replace($base, '', $u) . "   (linked from " . implode(', ', array_slice(array_unique($links[$u]), 0, 3)) . ")\n"; }
}
if (! $broken) { echo "no broken links\n"; }

echo "\n=== IMAGES CHECKED: " . count($images) . "\n";
$missing = 0;
foreach ($images as $src => $on) {
    [$st] = fetch($src, true);
    if ($st !== 200) { $missing++; echo "MISSING $st  " . str_replace($base, '', $src) . "   (on " . implode(', ', array_slice(array_unique($on), 0, 3)) . ")\n"; }
}
if (! $missing) { echo "no missing images\n"; }

$titles = [];
foreach ($pages as $path => $p) { if (! empty($p['title'])) { $titles[$p['title']][] = $path; } }
$dupes = array_filter($titles, fn ($x) => count($x) > 1);
echo "\n=== PAGES SHARING A TITLE: " . count($dupes) . "\n";
foreach ($dupes as $t => $ps) { echo "  \"$t\" -> " . implode(', ', $ps) . "\n"; }
if (! $dupes) { echo "every page has its own title\n"; }
