<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The lists AutoBazaar cannot work without.
 *
 * Roles and permissions (or nobody can sign in), the auto master data behind
 * every dropdown in the admin panel, and the places used by registration and by
 * the mobile app.
 *
 * It holds no personal data of any kind: no customers, no listings, no orders,
 * no phone numbers. Only lists. The files it reads are in seeders/data and were
 * exported from the live database.
 *
 * Safe to run twice. A table that already has rows is left alone, so seeding a
 * database that is already in use changes nothing.
 */
class ReferenceDataSeeder extends Seeder
{
    /** Small lists, kept as readable JSON. Order matters: parents first. */
    private const JSON_TABLES = [
        'roles',
        'permissions',
        'role_has_permissions',
        'auto_brands',
        'auto_fuel_types',
        'auto_owners',
        'auto_transmission_types',
        'auto_price_expectations',
        'auto_sellers',
        'auto_financiar',
        'auto_models',
        'countries',
        'tbl_auto_cities',
    ];

    /** Tens of thousands of rows each, so gzipped CSV rather than JSON. */
    private const CSV_TABLES = [
        'states',
        'cities',
        'tbl_auto_areas',
    ];

    public function run(): void
    {
        foreach (self::JSON_TABLES as $table) {
            $this->fill($table, fn () => $this->fromJson($table));
        }

        foreach (self::CSV_TABLES as $table) {
            $this->fill($table, fn () => $this->fromCsv($table));
        }
    }

    /** Only fills an empty table, so running this again is harmless. */
    private function fill(string $table, callable $rows): void
    {
        if (DB::table($table)->exists()) {
            $this->command?->line("  <fg=yellow>skipped</> {$table} — it already has rows");

            return;
        }

        $count = 0;
        foreach ($rows() as $chunk) {
            DB::table($table)->insert($chunk);
            $count += count($chunk);
        }

        $this->command?->line("  <fg=green>seeded</>  {$table} — " . number_format($count) . ' rows');
    }

    private function path(string $file): string
    {
        return database_path('seeders/data/' . $file);
    }

    /** @return iterable<array<int, array<string, mixed>>> */
    private function fromJson(string $table): iterable
    {
        $rows = json_decode(file_get_contents($this->path("{$table}.json")), true) ?: [];

        foreach (array_chunk($rows, 500) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Reads the gzipped CSV a chunk at a time — the city list is 47,941 rows and
     * holding it all in memory is pointless.
     *
     * @return iterable<array<int, array<string, mixed>>>
     */
    private function fromCsv(string $table): iterable
    {
        $handle = gzopen($this->path("{$table}.csv.gz"), 'rb');
        $columns = str_getcsv(trim(gzgets($handle)));

        $chunk = [];
        while (($line = gzgets($handle)) !== false) {
            $line = rtrim($line, "\r\n");
            if ($line === '') { continue; }

            $values = str_getcsv($line);
            $row = [];
            foreach ($columns as $i => $column) {
                $value = $values[$i] ?? null;
                $row[$column] = ($value === '') ? null : $value;
            }
            $chunk[] = $row;

            if (count($chunk) === 1000) {
                yield $chunk;
                $chunk = [];
            }
        }

        gzclose($handle);

        if ($chunk) { yield $chunk; }
    }
}
