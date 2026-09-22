<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the columns the admin lists, dashboard and mobile APIs filter on.
 * Adds indexes only: no column, data or API behaviour changes.
 * Every index is guarded, so a table/column missing on one server is skipped
 * instead of failing the whole migration.
 */
return new class extends Migration
{
    /** table => list of column sets */
    private array $indexes = [
        'auto_posts' => [
            ['auto_usage_status', 'auto_status'],
            ['auto_status'],
            ['user_id'],
            ['created_at'],
        ],
        'users' => [
            ['phone_number'],
            ['created_at'],
            ['status'],
        ],
        'customers' => [
            ['phone'],
        ],
        'auto_otps' => [
            ['phone'],
        ],
        'auto_enquiries' => [
            ['created_at'],
        ],
        'quotations' => [
            ['created_at'],
        ],
        'driver_requests' => [
            ['status'],
            ['created_at'],
        ],
        'auto_emergency_services' => [
            ['status'],
        ],
        'auto_rto_services' => [
            ['status'],
        ],
        'sos_alerts' => [
            ['status'],
        ],
        'sparepart_orders' => [
            ['order_status'],
            ['user_id'],
        ],
        'ride_requests' => [
            ['created_at'],
            ['booking_type', 'status'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $sets) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($sets as $columns) {
                if (! Schema::hasColumns($table, $columns)) {
                    continue;
                }

                $name = $this->indexName($table, $columns);

                if (Schema::hasIndex($table, $columns) || Schema::hasIndex($table, $name)) {
                    continue;
                }

                try {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($columns, $name));
                } catch (\Throwable $e) {
                    // e.g. a TEXT column on an older server. Not worth blocking a deploy.
                    Log::warning('performance_indexes.skipped', ['index' => $name, 'message' => $e->getMessage()]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $sets) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($sets as $columns) {
                $name = $this->indexName($table, $columns);

                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
                }
            }
        }
    }

    private function indexName(string $table, array $columns): string
    {
        return 'perf_' . $table . '_' . implode('_', $columns) . '_idx';
    }
};
