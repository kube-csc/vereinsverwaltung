<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('applications')
            ->where('status', 'in_pruefung')
            ->whereNotNull('funding_pot_id')
            ->orderBy('id')
            ->chunkById(100, function ($applications): void {
                foreach ($applications as $application) {
                    $answers = json_decode($application->answers ?? '[]', true);
                    $requestedAmount = $answers['beantragter_zuschuss'] ?? null;

                    if (! is_numeric($requestedAmount) || (float) $requestedAmount <= 0) {
                        continue;
                    }

                    $hasActiveBinding = DB::table('funding_pot_bindings')
                        ->where('application_id', $application->id)
                        ->whereIn('art', ['reserviert', 'bewilligt'])
                        ->exists();

                    if ($hasActiveBinding) {
                        continue;
                    }

                    DB::table('funding_pot_bindings')->insert([
                        'funding_pot_id' => $application->funding_pot_id,
                        'art' => 'reserviert',
                        'betrag' => round((float) $requestedAmount, 2),
                        'application_id' => $application->id,
                        'referenz' => 'Beantragter Zuschuss (Bestandsantrag)',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('funding_pot_bindings')
            ->where('art', 'reserviert')
            ->where('referenz', 'Beantragter Zuschuss (Bestandsantrag)')
            ->delete();
    }
};
