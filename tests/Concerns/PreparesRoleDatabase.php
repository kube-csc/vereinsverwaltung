<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Baut eine isolierte In-Memory-SQLite-Datenbank mit Benutzer- und Rollentabellen auf,
 * damit die Entwicklungsdatenbank nie angefasst wird.
 */
trait PreparesRoleDatabase
{
    protected function prepareRoleDatabase(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'jetstream.features' => [],
        ]);
        DB::purge();

        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        Artisan::call('migrate', [
            '--path' => [
                'database/migrations/2014_10_12_000000_create_users_table.php',
                'database/migrations/2026_10_17_130000_create_roles_table.php',
                'database/migrations/2026_10_17_130100_create_role_user_table.php',
                'database/migrations/2026_10_17_130200_create_permissions_table.php',
                'database/migrations/2026_10_17_130300_create_permission_role_table.php',
                'database/migrations/2026_05_11_204044_create_invitations_table.php',
                'database/migrations/2026_10_17_140000_create_invitation_role_table.php',
                'database/migrations/2026_10_17_150000_create_bank_accounts_table.php',
                'database/migrations/2026_10_17_160000_create_funding_programs_table.php',
                'database/migrations/2026_10_17_160100_create_funding_pots_table.php',
                'database/migrations/2026_10_17_160200_create_funding_pot_bindings_table.php',
                'database/migrations/2026_10_18_090000_create_application_types_table.php',
                'database/migrations/2026_10_18_090100_create_application_fields_table.php',
                'database/migrations/2026_10_18_090200_create_application_type_funding_pot_table.php',
                'database/migrations/2022_09_26_010659_create_clubs_table.php',
                'database/migrations/2026_10_18_220000_create_applications_table.php',
                'database/migrations/2026_10_18_220100_create_application_documents_table.php',
                'database/migrations/2026_10_18_220200_create_application_events_table.php',
                'database/migrations/2026_10_18_220300_create_notice_templates_table.php',
            ],
            '--force' => true,
        ]);

        Artisan::call('view:clear');
    }
}
