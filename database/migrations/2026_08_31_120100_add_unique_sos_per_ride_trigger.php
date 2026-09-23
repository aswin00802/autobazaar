<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One SOS per ride per person — so a repeated tap on the panic button does not
 * raise the same alarm twice.
 *
 * This is deliberately careful about existing rows. Both the live database and
 * the development one already hold repeats from before the rule existed, and
 * adding the key on top of them fails outright, which would stop `migrate` dead
 * partway through a deploy.
 *
 * Rather than delete anybody's emergency records to force it through, it checks
 * first and steps aside if it finds repeats, saying exactly which ride they are
 * on. Clear those by hand when you are ready and run `migrate` again — it will
 * pick this up then.
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->alreadyThere()) {
            return;
        }

        $repeats = DB::table('sos_alerts')
            ->select('ride_id', 'triggered_by', DB::raw('COUNT(*) as total'))
            ->groupBy('ride_id', 'triggered_by')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($repeats->isNotEmpty()) {
            $where = $repeats
                ->map(fn ($r) => 'ride ' . ($r->ride_id ?? 'none') . ' (' . ($r->triggered_by ?? 'unknown') . ' ×' . $r->total . ')')
                ->implode(', ');

            echo "\n  Skipped the one-SOS-per-ride rule: there are already repeats on {$where}.\n"
               . "  Nothing was changed or deleted. Tidy those rows, then run migrate again.\n\n";

            return;
        }

        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->unique(['ride_id', 'triggered_by'], 'sos_alerts_ride_trigger_unique');
        });
    }

    public function down(): void
    {
        if (! $this->alreadyThere()) {
            return;
        }

        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->dropUnique('sos_alerts_ride_trigger_unique');
        });
    }

    private function alreadyThere(): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'sos_alerts')
            ->where('INDEX_NAME', 'sos_alerts_ride_trigger_unique')
            ->exists();
    }
};
