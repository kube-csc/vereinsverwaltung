<?php

namespace Tests\Feature;

use App\Http\Controllers\RegattaRaffleController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Verifiziert die Pokal-Platzierungslogik in RegattaRaffleController::getFinalTeamlinks().
 *
 * Kernregel: Der Rang eines Teams in der Pokal-Anzeige muss der echten Platzierung
 * im Vor-Event entsprechen – unabhängig davon, ob weiter vorn platzierte Teams
 * keinen gültigen teamlink besitzen oder im aktuellen Event nicht gemeldet sind.
 */
class FinalTeamTrophyPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // FK-Checks deaktivieren, damit wir Testdaten ohne vollständige Elterntabellen einfügen können.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }

    protected function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Hilfsmethode: Aufruf der privaten getFinalTeamlinks()-Methode
    // -------------------------------------------------------------------------
    private function callGetFinalTeamlinks(int $regattaId): array
    {
        $controller = new RegattaRaffleController();
        return (function (int $id) {
            return $this->getFinalTeamlinks($id);
        })->call($controller, $regattaId);
    }

    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------
    private function seedGroup(): int
    {
        return DB::table('event_groups')->insertGetId([
            'termingruppe' => 'Testgruppe',
            'user_id'      => 1,
            'bearbeiter_id'=> 1,
            'visible'      => 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    private function seedEvent(int $groupId, string $date): int
    {
        return DB::table('events')->insertGetId([
            'datumvon'     => $date,
            'datumbis'     => $date,
            'ueberschrift' => "Event $date",
            'startseite'   => '',
            'regatta'      => 1,
            'verwendung'   => 0,
            'eventGroup_id'=> $groupId,
            'autor_id'     => 1,
            'bearbeiter_id'=> 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    private function seedTable(int $eventId): int
    {
        return DB::table('tabeles')->insertGetId([
            'event_id'            => $eventId,
            'gruppe_id'           => 1,
            'tabelleDatumVon'     => '2025-06-01',
            'ueberschrift'        => 'A-Finale',
            'finale'              => 1,
            'tabelleLevelVon'     => 1,
            'tabelleLevelBis'     => 1,
            'wertungsart'         => 1,
            'buchholzwertungaktiv'=> 0,
            'tabelleVisible'      => 1,
            'finaleAnzeigen'      => '19:00:00',
            'getrenntewertung'    => 0,
            'autor_id'            => 1,
            'bearbeiter_id'       => 1,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    private function seedTeam(int $regattaId, int $teamlink): int
    {
        return DB::table('regatta_teams')->insertGetId([
            'regatta_id'  => $regattaId,
            'teamname'    => "Team TL{$teamlink}",
            'verein'      => 'Testverein',
            'teamcaptain' => 'Captain',
            'strasse'     => 'Str. 1',
            'plz'         => '12345',
            'ort'         => 'Stadt',
            'telefon'     => '0123456',
            'email'       => "team{$teamlink}@test.de",
            'status'      => 'Neuanmeldung',
            'passwort'    => 'pass',
            'mailen'      => 'nein',
            'gruppe_id'   => 1,
            'teamlink'    => $teamlink,
            'training'    => 0,
            'datum'       => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function seedTabledata(int $regattaId, int $tableId, int $teamId, int $punkte): void
    {
        DB::table('tabledatas')->insert([
            'regatta_id'           => $regattaId,
            'tabele_id'            => $tableId,
            'mannschaft_id'        => $teamId,
            'punkte'               => $punkte,
            'zeit'                 => '00:00:00',
            'hundert'              => 0,
            'zeitpunktegleich'     => '00:00:00',
            'hundertpunktegleich'  => 0,
            'buchholzzahl'         => 0,
            'rennanzahl'           => 3,
            'autor_id'             => 1,
            'bearbeiter_id'        => 1,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    // =========================================================================
    // TESTS
    // =========================================================================

    /**
     * Normalfall: Alle Teams haben teamlink > 0.
     * Erwartet: Ranking entspricht der Punkte-Reihenfolge exakt.
     */
    public function test_all_teams_have_teamlink_correct_ranking(): void
    {
        $gid        = $this->seedGroup();
        $lastId     = $this->seedEvent($gid, '2025-06-01');
        $currentId  = $this->seedEvent($gid, '2026-06-01');
        $tableId    = $this->seedTable($lastId);

        $teamA = $this->seedTeam($lastId, 10); // Platz 1
        $teamB = $this->seedTeam($lastId, 20); // Platz 2

        $this->seedTabledata($lastId, $tableId, $teamA, 100);
        $this->seedTabledata($lastId, $tableId, $teamB, 80);

        $results = $this->callGetFinalTeamlinks($currentId);

        $this->assertArrayHasKey(10, $results);
        $this->assertArrayHasKey(20, $results);
        $this->assertSame(1, $results[10]['platz'], 'Team A (100 Pkt) muss Platz 1 sein');
        $this->assertSame(2, $results[20]['platz'], 'Team B (80 Pkt) muss Platz 2 sein');
    }

    /**
     * KERNTEST des Bugfixes:
     * Platz 1 hat teamlink = 0, Platz 2 hat teamlink = 20.
     * Erwartet: Team B bleibt Platz 2 – darf nicht fälschlich zu Platz 1 aufsteigen.
     */
    public function test_place_1_without_teamlink_does_not_promote_place_2_to_place_1(): void
    {
        $gid       = $this->seedGroup();
        $lastId    = $this->seedEvent($gid, '2025-06-01');
        $currentId = $this->seedEvent($gid, '2026-06-01');
        $tableId   = $this->seedTable($lastId);

        $teamA = $this->seedTeam($lastId, 0);  // Platz 1 – aber kein teamlink
        $teamB = $this->seedTeam($lastId, 20); // Platz 2 – mit gültigem teamlink

        $this->seedTabledata($lastId, $tableId, $teamA, 100); // Platz 1
        $this->seedTabledata($lastId, $tableId, $teamB, 80);  // Platz 2

        $results = $this->callGetFinalTeamlinks($currentId);

        $this->assertArrayNotHasKey(0, $results,
            'teamlink=0 darf nicht im Ergebnis auftauchen');
        $this->assertArrayHasKey(20, $results,
            'Team B (teamlink=20) muss im Ergebnis sein');
        $this->assertSame(2, $results[20]['platz'],
            'Team B MUSS Platz 2 behalten – kein Aufrücken, weil Platz 1 kein teamlink hat!');
    }

    /**
     * Drei Teams: Platz 1 + 2 ohne teamlink, nur Platz 3 mit teamlink.
     * Erwartet: Platz 3 bleibt Platz 3, rückt nicht zu Platz 1 auf.
     */
    public function test_multiple_teams_without_teamlink_at_top_do_not_change_rank(): void
    {
        $gid       = $this->seedGroup();
        $lastId    = $this->seedEvent($gid, '2025-06-01');
        $currentId = $this->seedEvent($gid, '2026-06-01');
        $tableId   = $this->seedTable($lastId);

        $teamA = $this->seedTeam($lastId, 0);  // Platz 1, kein teamlink
        $teamB = $this->seedTeam($lastId, 0);  // Platz 2, kein teamlink
        $teamC = $this->seedTeam($lastId, 30); // Platz 3 mit teamlink=30

        $this->seedTabledata($lastId, $tableId, $teamA, 100);
        $this->seedTabledata($lastId, $tableId, $teamB, 90);
        $this->seedTabledata($lastId, $tableId, $teamC, 80);

        $results = $this->callGetFinalTeamlinks($currentId);

        $this->assertArrayHasKey(30, $results);
        $this->assertSame(3, $results[30]['platz'],
            'Team C (Platz 3) darf nicht zu Platz 1 werden, auch wenn Pl.1+2 kein teamlink haben');
    }

    /**
     * Tabellenbezeichnung wird korrekt übertragen.
     */
    public function test_table_name_is_stored_correctly(): void
    {
        $gid       = $this->seedGroup();
        $lastId    = $this->seedEvent($gid, '2025-06-01');
        $currentId = $this->seedEvent($gid, '2026-06-01');
        $tableId   = $this->seedTable($lastId);

        $teamA = $this->seedTeam($lastId, 10);
        $this->seedTabledata($lastId, $tableId, $teamA, 50);

        $results = $this->callGetFinalTeamlinks($currentId);

        $this->assertArrayHasKey(10, $results);
        $this->assertSame('A-Finale', $results[10]['tabelle']);
    }
}

