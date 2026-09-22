<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Everything a fresh install needs, and nothing more.
 *
 *     php artisan db:seed
 *
 * Run it on a database that already has data and it changes nothing — each
 * seeder leaves a table alone once it has rows in it.
 *
 * What this puts in:
 *   - roles, permissions and who is allowed what
 *   - one Super Admin to sign in with
 *   - the auto master data behind every dropdown
 *   - countries, states, cities and areas
 *   - the website's vehicle catalogue
 *   - the FairPrice cancellation reasons
 *
 * What it deliberately leaves out: customers, their phone numbers, auto
 * listings, enquiries, quotations and orders. That is real people's data and it
 * does not belong in a repository.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Order matters: the catalogue below needs brands and fuel types.
            ReferenceDataSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            VehicleCatalogSeeder::class,
            CancelReasonSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Done. The site is ready to open.');
    }
}
