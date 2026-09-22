<?php

namespace App\Support;

/**
 * The website's shared data ($site): brand, menu, contact details, social links.
 *
 * Most of it still lives in resources/fixtures/site.php. The parts an owner needs
 * to change without a developer come from Admin > Settings > General:
 *
 *   business_mobile   -> the phone shown everywhere, the tap-to-call link and WhatsApp
 *   business_email    -> contact email
 *   business_address  -> address
 *   social_facebook / social_instagram / social_youtube / social_twitter / social_linkedin
 *
 * Rule: a filled admin value wins; an empty one leaves the fixture value in place.
 * So with nothing saved the site is exactly as before, and clearing a field is
 * the undo. Every page reads $site through here, so they all change together.
 *
 * Working hours intentionally stay in the fixture. The website shows ONE phone
 * number (Business Mobile); the fixture's old second number is not displayed.
 */
class SiteData
{
    /** admin setting key => [label, icon name in x-ui.icon] in display order */
    public const SOCIAL_NETWORKS = [
        'social_instagram' => ['Instagram', 'instagram'],
        'social_facebook'  => ['Facebook', 'facebook'],
        'social_youtube'   => ['YouTube', 'youtube'],
        'social_twitter'   => ['X (Twitter)', 'twitter'],
        'social_linkedin'  => ['LinkedIn', 'linkedin'],
    ];

    private static ?array $memo = null;

    public static function site(): array
    {
        if (self::$memo !== null) {
            return self::$memo;
        }

        $site = require resource_path('fixtures/site.php');

        $site['contact'] = self::contact($site['contact'] ?? []);
        $site['socials'] = self::socials($site['socials'] ?? [], $site['contact']);

        return self::$memo = $site;
    }

    /** Test helper / long-running workers: forget the per-request copy. */
    public static function flush(): void
    {
        self::$memo = null;
    }

    /**
     * What the admin form should show in an empty field: the value the website
     * is using right now, so the owner edits the live value instead of a blank.
     */
    public static function currentValue(string $settingKey): string
    {
        $fixture = require resource_path('fixtures/site.php');

        return match ($settingKey) {
            'business_mobile'  => self::digits($fixture['contact']['phone'] ?? ''),
            'business_email'   => (string) ($fixture['contact']['email'] ?? ''),
            'business_address' => (string) ($fixture['contact']['address'] ?? ''),
            default => (string) (collect($fixture['socials'] ?? [])
                ->firstWhere('icon', self::SOCIAL_NETWORKS[$settingKey][1] ?? '__none__')['url'] ?? ''),
        };
    }

    private static function contact(array $contact): array
    {
        $mobile = self::digits((string) getSetting('business_mobile'));

        // Accept "8608860893", "+91 86088 60893" or "918608860893": keep the 10-digit national number.
        if (strlen($mobile) === 12 && str_starts_with($mobile, '91')) {
            $mobile = substr($mobile, 2);
        }

        if (strlen($mobile) === 10) {
            $contact['phone']      = substr($mobile, 0, 5) . ' ' . substr($mobile, 5);   // 86088 60893
            $contact['phone_e164'] = '+91' . $mobile;                                     // tel: links
            $contact['whatsapp']   = '91' . $mobile;                                      // wa.me links
        }

        // The owner asked for a single number on the website, so the old second
        // line is dropped here rather than left to drift out of date.
        $contact['phone_alt'] = '';

        $email = trim((string) getSetting('business_email'));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contact['email'] = $email;
        }

        $address = trim((string) getSetting('business_address'));
        if ($address !== '') {
            $contact['address'] = preg_replace('/\s+/', ' ', $address);
        }

        return $contact;
    }

    private static function socials(array $fixtureSocials, array $contact): array
    {
        $byIcon = collect($fixtureSocials)->keyBy('icon');
        $socials = [];

        foreach (self::SOCIAL_NETWORKS as $key => [$label, $icon]) {
            $url = trim((string) getSetting($key));

            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL) || ! preg_match('#^https?://#i', $url)) {
                $url = $byIcon[$icon]['url'] ?? '';   // nothing valid in admin: keep what the site had
            }

            if ($url !== '') {
                $socials[] = ['label' => $label, 'icon' => $icon, 'url' => $url];
            }
        }

        // WhatsApp always follows the business number, so the icon can never point at an old number.
        if (! empty($contact['whatsapp'])) {
            $socials[] = ['label' => 'WhatsApp', 'icon' => 'whatsapp', 'url' => 'https://wa.me/' . $contact['whatsapp']];
        }

        return $socials;
    }

    private static function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value);
    }
}
