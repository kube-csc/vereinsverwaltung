<?php

namespace Tests\Feature;

use App\Http\Controllers\RegattaRaffleController;
use Tests\TestCase;

/**
 * Verifiziert die Final-Seeding-Logik:
 * stärkste Teams in die Mitte, nach außen hin schwächer.
 */
class FinalLaneAssignmentTest extends TestCase
{
    public function test_calculate_seeded_lanes_for_four_lanes_places_strongest_in_middle()
    {
        $controller = new RegattaRaffleController();
        $seeded = array_map('intval', (function (int $lanesCount) {
            return $this->calculateSeededLanes($lanesCount);
        })->call($controller, 4));

        // Platz 1 -> Bahn 2, Platz 2 -> Bahn 3, Platz 3 -> Bahn 1, Platz 4 -> Bahn 4
        $this->assertSame([2, 3, 1, 4], $seeded);
    }

    public function test_four_team_final_maps_places_to_center_out_lanes()
    {
        $controller = new RegattaRaffleController();
        $laneAssignment = array_map('intval', (function (int $lanesCount) {
            return $this->calculateSeededLanes($lanesCount);
        })->call($controller, 4)); // [2,3,1,4]

        $startPlace = 1;
        $mapping = [];
        foreach ($laneAssignment as $idx => $lane) {
            $place = $startPlace + $idx;
            $mapping[$lane] = $place;
        }

        ksort($mapping);

        // Nach Bahn sortiert: B1->Platz3, B2->Platz1, B3->Platz2, B4->Platz4
        $this->assertSame([
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
        ], $mapping);
    }

    public function test_partial_final_uses_best_available_center_out_lanes_and_is_stable_per_race()
    {
        $controller = new RegattaRaffleController();
        $seededLanesFor = function (int $lanesCount) use ($controller) {
            return array_map('intval', (function (int $lanes) {
                return $this->calculateSeededLanes($lanes);
            })->call($controller, $lanesCount));
        };

        $full = $seededLanesFor(4); // [2,3,1,4]
        $partial = array_slice($full, 0, 3);          // 3 Teams -> [2,3,1]

        $this->assertSame([2, 3, 1], $partial);

        // Stabilität: pro Lauf neu berechnet darf keine "Vererbung" stattfinden.
        $firstRace = array_slice($seededLanesFor(4), 0, 3);
        $secondRace = array_slice($seededLanesFor(4), 0, 3);
        $this->assertSame($firstRace, $secondRace);
    }
}
