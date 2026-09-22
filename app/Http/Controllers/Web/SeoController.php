<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\VehicleCatalogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * sitemap.xml and robots.txt for the customer website.
 *
 * Both are generated rather than kept as files so they always carry the right
 * domain and the current catalogue. Outside production robots.txt blocks
 * everything, matching the noindex tag in site/partials/seo.blade.php.
 */
class SeoController extends Controller
{
    /** Public pages that always exist: route name => [change frequency, priority]. */
    private const STATIC_PAGES = [
        'site.home'             => ['daily', '1.0'],
        'site.new-autos'        => ['daily', '0.9'],
        'site.used-autos'       => ['daily', '0.9'],
        'site.offers'           => ['weekly', '0.8'],
        'site.finance'          => ['monthly', '0.8'],
        'site.compare'          => ['weekly', '0.7'],
        'site.accessories'      => ['weekly', '0.7'],
        'site.accessories.shop' => ['daily', '0.7'],
        'site.buying-options'   => ['monthly', '0.6'],
        'site.schemes'          => ['monthly', '0.6'],
        'site.news'             => ['weekly', '0.6'],
        'site.app'              => ['monthly', '0.5'],
        'site.about'            => ['yearly', '0.4'],
        'site.contact'          => ['yearly', '0.5'],
        'site.faq'              => ['monthly', '0.5'],
        'terms-conditions'      => ['yearly', '0.2'],
        'privacy-policy'        => ['yearly', '0.2'],
    ];

    public function sitemap(VehicleCatalogService $catalog)
    {
        $urls = Cache::remember('site_sitemap_urls', 3600, function () use ($catalog) {
            $urls = [];

            foreach (self::STATIC_PAGES as $route => [$frequency, $priority]) {
                if (\Illuminate\Support\Facades\Route::has($route)) {
                    $urls[] = ['loc' => route($route), 'changefreq' => $frequency, 'priority' => $priority];
                }
            }

            // Brand pages and every model page from the live catalogue.
            try {
                $vehicles = $catalog->all();

                foreach (collect($vehicles)->pluck('brand_slug')->filter()->unique() as $brand) {
                    $urls[] = ['loc' => route('site.brand', $brand), 'changefreq' => 'weekly', 'priority' => '0.8'];
                }

                foreach ($vehicles as $vehicle) {
                    if (! empty($vehicle['brand_slug']) && ! empty($vehicle['model_slug'])) {
                        $urls[] = [
                            'loc' => route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']]),
                            'changefreq' => 'weekly',
                            'priority' => '0.9',
                        ];
                    }
                }

                // The ready-made comparisons offered on the compare page (same pairs, same order).
                $list = array_values($vehicles);
                foreach ([[0, 1], [0, 2], [1, 2], [0, 5], [3, 4], [2, 5]] as [$a, $b]) {
                    if (isset($list[$a]['slug'], $list[$b]['slug'])) {
                        $urls[] = [
                            'loc' => route('site.compare.combo', $list[$a]['slug'] . '-vs-' . $list[$b]['slug']),
                            'changefreq' => 'weekly',
                            'priority' => '0.6',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('sitemap.catalogue_failed', ['message' => $e->getMessage()]);
            }

            // Every live used-auto listing.
            try {
                foreach (app(\App\Services\UsedAutoService::class)->all() as $listing) {
                    $urls[] = ['loc' => route('site.used-auto', [$listing['id'], $listing['slug']]), 'changefreq' => 'daily', 'priority' => '0.7'];
                }
            } catch (\Throwable $e) {
                Log::warning('sitemap.used_autos_failed', ['message' => $e->getMessage()]);
            }

            // News articles.
            try {
                $content = require resource_path('fixtures/content.php');

                foreach ($content['news'] ?? [] as $post) {
                    if (! empty($post['slug'])) {
                        $urls[] = ['loc' => route('site.news.article', $post['slug']), 'changefreq' => 'monthly', 'priority' => '0.5'];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('sitemap.news_failed', ['message' => $e->getMessage()]);
            }

            return $urls;
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>'
                  . '<changefreq>' . $url['changefreq'] . '</changefreq>'
                  . '<priority>' . $url['priority'] . '</priority></url>' . "\n";
        }

        $xml .= '</urlset>' . "\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots()
    {
        if (! app()->environment('production')) {
            // Test and staging copies must never appear in Google.
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $body = implode("\n", [
                'User-agent: *',
                'Disallow: /cart',
                'Disallow: /checkout',
                'Disallow: /account',
                'Disallow: /search',
                'Disallow: /user/',
                'Disallow: /login',
                'Disallow: /dashboard',
                'Disallow: /admin-search',
                'Allow: /',
                '',
                'Sitemap: ' . url('sitemap.xml'),
                '',
            ]);
        }

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
