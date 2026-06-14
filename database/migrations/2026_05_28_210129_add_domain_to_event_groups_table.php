<?php
// nicht im git gub nötig

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_groups', function (Blueprint $table) {
            // Nur anlegen, wenn die Spalte noch nicht existiert (z. B. weil eine ältere Migration sie bereits erstellt hat).

            if (!Schema::hasColumn('event_groups', 'bearbeiter_id')) {
                $column = $table->unsignedBigInteger('bearbeiter_id')->nullable();
                if (Schema::hasColumn('event_groups', 'user_id')) {
                    $column->after('user_id');
                } elseif (Schema::hasColumn('event_groups', 'termingruppe')) {
                    $column->after('termingruppe');
                }
            }

            if (!Schema::hasColumn('event_groups', 'domain')) {
                $column = $table->string('domain')->nullable();
                if (Schema::hasColumn('event_groups', 'visible')) {
                    $column->after('visible');
                }
            }

            if (!Schema::hasColumn('event_groups', 'liveDomain')) {
                $column = $table->string('liveDomain')->nullable();
                if (Schema::hasColumn('event_groups', 'domain')) {
                    $column->after('domain');
                } elseif (Schema::hasColumn('event_groups', 'visible')) {
                    $column->after('visible');
                }
            }

            if (!Schema::hasColumn('event_groups', 'headerTitel')) {
                $column = $table->string('headerTitel')->nullable();
                if (Schema::hasColumn('event_groups', 'liveDomain')) {
                    $column->after('liveDomain');
                } elseif (Schema::hasColumn('event_groups', 'domain')) {
                    $column->after('domain');
                }
            }

            if (!Schema::hasColumn('event_groups', 'headerSlogen')) {
                $column = $table->string('headerSlogen')->nullable();
                if (Schema::hasColumn('event_groups', 'headerTitel')) {
                    $column->after('headerTitel');
                } elseif (Schema::hasColumn('event_groups', 'liveDomain')) {
                    $column->after('liveDomain');
                } elseif (Schema::hasColumn('event_groups', 'domain')) {
                    $column->after('domain');
                }
            }

            if (!Schema::hasColumn('event_groups', 'headerBild')) {
                $column = $table->string('headerBild')->nullable();
                if (Schema::hasColumn('event_groups', 'headerSlogen')) {
                    $column->after('headerSlogen');
                } elseif (Schema::hasColumn('event_groups', 'headerTitel')) {
                    $column->after('headerTitel');
                } elseif (Schema::hasColumn('event_groups', 'liveDomain')) {
                    $column->after('liveDomain');
                } elseif (Schema::hasColumn('event_groups', 'domain')) {
                    $column->after('domain');
                }
            }

            if (!Schema::hasColumn('event_groups', 'accentColor')) {
                $column = $table->string('accentColor', 9)->nullable();
                if (Schema::hasColumn('event_groups', 'headerBild')) {
                    $column->after('headerBild');
                } elseif (Schema::hasColumn('event_groups', 'headerSlogen')) {
                    $column->after('headerSlogen');
                }
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Wenn die ältere Migration bereits gelaufen ist, dürfen wir hier nichts entfernen,
        // sonst würden wir Spalten droppen, die nicht von dieser Migration stammen.
        try {
            $olderMigrationRan = DB::table('migrations')
                ->where('migration', '2024_12_28_210129_add_domain_to_event_groups_table')
                ->exists();
        } catch (\Throwable $e) {
            // Falls keine DB-Verbindung verfügbar ist (z. B. in speziellen Tools), defensiv verhalten.
            $olderMigrationRan = true;
        }

        // Diese Spalte stammt sicher aus dieser Migration und darf immer zurückgerollt werden.
        Schema::table('event_groups', function (Blueprint $table) {
            if (Schema::hasColumn('event_groups', 'bearbeiter_id')) {
                $table->dropColumn('bearbeiter_id');
            }
        });

        if ($olderMigrationRan) {
            return;
        }

        Schema::table('event_groups', function (Blueprint $table) {
            if (Schema::hasColumn('event_groups', 'accentColor')) {
                $table->dropColumn('accentColor');
            }
            if (Schema::hasColumn('event_groups', 'headerBild')) {
                $table->dropColumn('headerBild');
            }
            if (Schema::hasColumn('event_groups', 'headerSlogen')) {
                $table->dropColumn('headerSlogen');
            }
            if (Schema::hasColumn('event_groups', 'headerTitel')) {
                $table->dropColumn('headerTitel');
            }
            if (Schema::hasColumn('event_groups', 'liveDomain')) {
                $table->dropColumn('liveDomain');
            }
            if (Schema::hasColumn('event_groups', 'domain')) {
                $table->dropColumn('domain');
            }
        });
    }
};
