<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The handful of settings rows the site reads on every page.
 *
 * The logo keys are left empty on purpose. Uploaded pictures are not kept in
 * Git, so pointing at one would only produce a broken image — empty makes the
 * site fall back to the logo built into the theme. Upload your own under
 * Settings, General and these fill themselves in.
 *
 * No payment keys here either. Enter those under Settings, Payments, where they
 * are stored encrypted.
 */
class SettingsSeeder extends Seeder
{
    private const DEFAULTS = [
        'business_name' => 'AutoBazaar',
        'footer_text'   => 'AutoBazaar',
        'admin_logo'    => '',
        'web_logo'      => '',
        'fav_icon'      => '',
    ];

    public function run(): void
    {
        $added = 0;

        foreach (self::DEFAULTS as $key => $value) {
            if (DB::table('settings')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'status_id'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        Setting::flushCache();

        $this->command?->line($added
            ? "  <fg=green>seeded</>  settings — {$added} rows"
            : '  <fg=yellow>skipped</> settings — already set up');
    }
}
