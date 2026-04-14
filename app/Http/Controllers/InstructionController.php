<?php

namespace App\Http\Controllers;

use App\Models\Instruction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructionController extends Controller
{
    /**
     * Verschiebt alle Menü-Einträge (hauptmenu 1..3) in Spalten links von $fromCol um $delta.
     * Bestimmte Instruction-IDs können dabei ausgeschlossen werden (z.B. weil sie bereits
     * separat in eine Zielspalte umgehängt wurden).
     */
    private function shiftMenuColumnsLeftOf(int $fromCol, array $excludeIds = [], int $delta = 10): void
    {
        if ($delta === 0) {
            return;
        }

        $query = Instruction::whereIn('hauptmenu', [1, 2, 3])
            ->where('hauptmenuspalte', '<', $fromCol);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $others = $query->get(['id', 'hauptmenuspalte']);

        foreach ($others as $col) {
            Instruction::where('id', $col->id)
                ->update([
                    'hauptmenuspalte' => $col->hauptmenuspalte + $delta,
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now(),
                ]);
        }
    }

    /**
     * Erhöht die Position aller Unterpunkte (hauptmenu=3), die VOR $fromPosition liegen,
     * um $delta. Bestimmte IDs können ausgeschlossen werden (z.B. die eigene Spalte/der
     * gerade geklickte Datensatz).
     *
     * Hinweis: Diese Logik ist bewusst "global" wie im bisherigen Code (ohne Filter auf hauptmenuspalte).
     */
    private function shiftHm3PositionsBefore(int $fromPosition, array $includeIds = [], int $delta = 10): void
    {
        if ($delta === 0 || $fromPosition <= 0 || $includeIds === []) {
            return;
        }

        $others = Instruction::whereIn('id', $includeIds)
            ->where('position', '<', $fromPosition)
            ->get(['id', 'position']);

        foreach ($others as $col) {
            Instruction::where('id', $col->id)
                ->update([
                    'position' => $col->position + $delta,
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now(),
                ]);
        }
    }

    /**
     * Block-Konsistenz: Wenn ein Container (hauptmenu=2) die Menüspalte wechselt,
     * müssen alle zugehörigen Unterpunkte (hauptmenu=3) mitgezogen werden.
     *
     * Da das Datenmodell keine explizite Parent-ID hat, gilt: "zugehörige Unterpunkte" =
     * alle HM=3 Einträge, die aktuell in der alten Spalte hängen.
     */
    private function moveChildrenToColumn(int $oldColumn, int $newColumn): void
    {
        if ($oldColumn === $newColumn) {
            return;
        }

        Instruction::where('hauptmenu', 3)
            ->where('hauptmenuspalte', $oldColumn)
            ->update([
                'hauptmenuspalte' => $newColumn,
            ]);
    }

    /**
     * Normalisiert Positionen innerhalb einer Menü-Spalte.
     * Wichtig: Ein Sammelmenü-Container (hauptmenu==2) soll immer vor seinen Unterpunkten stehen.
     */
    private function normalizeMenuColumn(int $hauptmenuspalte): void
    {
        Log::debug('instruction.normalizeMenuColumn: start', [
            'hauptmenuspalte' => $hauptmenuspalte,
        ]);

        // Block-Konsistenz: Falls es (durch alte Logik/Fehler) Kinder (hauptmenu=3) ohne passenden
        // Container (hauptmenu=2) in dieser Spalte gibt, normalisieren wir trotzdem die Reihenfolge.
        // Regeln:
        // - Container (HM=2) steht vor seinen Einträgen
        // - HM=1 und HM=2 werden bzgl. Positionen gleich behandelt (jede Position nur einmal)
        // - Positionen laufen in 10er Schritten durch und werden eindeutig vergeben.
        $instructions = Instruction::where('hauptmenuspalte', $hauptmenuspalte)
            ->orderByRaw("CASE WHEN hauptmenu = 2 THEN 0 ELSE 1 END")
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $positionNew = 10;
        foreach ($instructions as $instruction) {
            Instruction::findOrFail($instruction->id)->update([
                'position' => $positionNew,
            ]);
            $positionNew += 10;
        }

        Log::debug('instruction.normalizeMenuColumn: end', [
            'hauptmenuspalte' => $hauptmenuspalte,
            'count' => $instructions->count(),
        ]);
    }

    /**
     * Verschiebt eine komplette Menü-Gruppe (alle Instructions einer `hauptmenuspalte`) innerhalb
     * der globalen Menüreihenfolge nach oben oder unten, ohne die interne Reihenfolge zu zerstören.
     */
    private function moveMenuColumn(int $hauptmenuspalte, int $direction): void
    {
        Log::info('instruction.moveMenuColumn: start', [
            'source_hauptmenuspalte' => $hauptmenuspalte,
            'direction' => $direction,
        ]);

        $all = Instruction::where('hauptmenuspalte', '>', 0)
            ->orderBy('hauptmenuspalte')
            ->orderBy('position')
            ->get();

        // Fallback: Wenn es noch keine Menü-Struktur gibt (z.B. alle hauptmenuspalte/position = 0),
        // bootstrappe eine sinnvolle Startbasis, von der aus man anschließend verschieben kann.
        if ($all->isEmpty()) {
            Log::warning('instruction.moveMenuColumn: no menu columns found -> bootstrap fallback', [
                'source_hauptmenuspalte' => $hauptmenuspalte,
            ]);

            $candidates = Instruction::orderBy('id')->get();
            if ($candidates->isEmpty()) {
                return;
            }

            // Setze alle Einträge in die erste Spalte (10) und normalisiere Positionen.
            // Prinzip: Container (hauptmenu==2) zuerst, danach Kinder und sonstige.
            $col = 10;
            Instruction::query()->update([
                'hauptmenuspalte' => $col,
            ]);

            // Wenn noch nie Hauptmenu-Werte gesetzt wurden (alles 0),
            // setze den ersten Datensatz als Top-Level Link, damit es einen Einstieg gibt.
            $anyNonZeroHauptmenu = Instruction::where('hauptmenu', '!=', 0)->exists();
            if (!$anyNonZeroHauptmenu) {
                Instruction::findOrFail($candidates->first()->id)->update([
                    'hauptmenu' => 1,
                ]);
            }

            $this->normalizeMenuColumn($col);

            // Nach Bootstrap: setze die Quelle auf die einzige existierende Spalte.
            $hauptmenuspalte = $col;
            $all = Instruction::where('hauptmenuspalte', '>', 0)
                ->orderBy('hauptmenuspalte')
                ->orderBy('position')
                ->get();
        }

        $columns = $all->pluck('hauptmenuspalte')->unique()->values();
        $idx = $columns->search($hauptmenuspalte);
        if ($idx === false) {
            Log::warning('instruction.moveMenuColumn: column not found', [
                'source_hauptmenuspalte' => $hauptmenuspalte,
                'columns' => $columns->all(),
            ]);
            return;
        }

        $targetIndex = $idx + $direction;
        if ($targetIndex < 0 || $targetIndex >= $columns->count()) {
            Log::debug('instruction.moveMenuColumn: out of bounds', [
                'source_hauptmenuspalte' => $hauptmenuspalte,
                'idx' => $idx,
                'targetIndex' => $targetIndex,
                'columns' => $columns->all(),
            ]);
            return;
        }

        $columns->splice($idx, 1);
        $columns->splice($targetIndex, 0, [$hauptmenuspalte]);

        $before = $columns->all();

        $new = 10;
        foreach ($columns as $col) {
            $old = (int)$col;

            // Container/Top-Level Einträge der Spalte umhängen.
            Instruction::where('hauptmenuspalte', $old)->update([
                'hauptmenuspalte' => $new,
            ]);

            // Sicherheit: Falls es HM=3 "Kinder" gab, die noch auf der alten Spalte stehen,
            // ziehe sie ebenfalls um (Block-Konsistenz).
            $this->moveChildrenToColumn($old, $new);

            $this->normalizeMenuColumn($new);
            $new += 10;
        }

        Log::info('instruction.moveMenuColumn: end', [
            'before' => $before,
            'after' => Instruction::where('hauptmenuspalte', '>', 0)
                ->orderBy('hauptmenuspalte')
                ->pluck('hauptmenuspalte')
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    /**
     * Normalisiert die Positionen für "Informationsseiten ohne Menü" (hauptmenu == 0)
     * in 10er-Schritten, damit eine saubere Sortierung möglich ist.
     */
    private function normalizeInfoPagesWithoutMenu(): void
    {
        $instructions = Instruction::where('hauptmenu', 0)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $positionNew = 10;
        foreach ($instructions as $instruction) {
            if ((int)$instruction->position !== $positionNew) {
                Instruction::findOrFail($instruction->id)->update([
                    'position' => $positionNew,
                ]);
            }
            $positionNew += 10;
        }
    }

    /**
     * Normalisiert die komplette Menüspalten-Reihenfolge (hauptmenuspalte) über alle Einträge,
     * damit es keine Lücken gibt und die Spalten wieder sauber in 10er-Schritten laufen.
     *
     * Gilt explizit für hauptmenu = 1..3 (Menüstruktur). Einträge mit hauptmenu=0 bleiben unberührt.
     */
    private function normalizeAllMenuColumns(): void
    {
        $columns = Instruction::whereIn('hauptmenu', [1, 2, 3])
            ->where('hauptmenuspalte', '>', 0)
            ->orderBy('hauptmenuspalte')
            ->pluck('hauptmenuspalte')
            ->unique()
            ->values();

        $new = 10;
        foreach ($columns as $oldCol) {
            $old = (int)$oldCol;
            if ($old !== $new) {
                Instruction::whereIn('hauptmenu', [1, 2, 3])
                    ->where('hauptmenuspalte', $old)
                    ->update([
                        'hauptmenuspalte' => $new,
                    ]);
            }

            $this->normalizeMenuColumn($new);
            $new += 10;
        }
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function aktiv($instruction_id)
    {
        Instruction::findOrFail($instruction_id)->update([
            'visible' => '1',
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);
        return Redirect()->back()->with('success', 'Informationsseite wurde sichtbar geschaltet.');
    }

    public function inaktiv($instruction_id)
    {
        $instruction = Instruction::findOrFail($instruction_id);
        if ($instruction->hauptmenu == 0) {
            Instruction::findOrFail($instruction_id)->update([
                'visible' => '0',
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);
        } else {
            Instruction::where('hauptmenuspalte', $instruction->hauptmenuspalte)
                ->where('visible', '1')
                ->update([
                    'visible' => '0',
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now()
                ]);
        }

        return Redirect()->back()->with('success', 'Die Informationsseite wurde unsichtbar geschaltet.');
    }

    public function maxtop($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        $positionFilter = $instruction->hauptmenuspalte;

        if ((int)$instruction->hauptmenu === 1) {
            $fromCol = (int)$instruction->hauptmenuspalte;

            Instruction::findOrFail($instructionId)->update([
                'hauptmenuspalte' => 10,
                'position' => 10,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            // Alle anderen Menüpunkte (HM=1..3) sollen um +10 in der hauptmenuspalte verschoben werden.
            // Der geklickte Datensatz ($instructionId) wird dabei NICHT mehr verändert.
            // Kollisionen vermeiden wir durch Updates in absteigender Spalten-Reihenfolge.
            // Regel: Merke dir die ursprüngliche Spalte ($fromCol) des geklickten Datensatzes.
            // Beim Erhöhen der hauptmenuspalte werden nur Spalten < $fromCol verschoben.
            // ("Stoppe", sobald du bei >= $fromCol angekommen bist.)
            $colsToShift = Instruction::whereIn('hauptmenu', [1, 2, 3])
                ->where('hauptmenuspalte', '>=', 10)
                ->where('hauptmenuspalte', '<', $fromCol)
                ->pluck('hauptmenuspalte')
                ->unique()
                ->sortDesc()
                ->values();

            foreach ($colsToShift as $col) {
                $colInt = (int)$col;

                Instruction::whereIn('hauptmenu', [1, 2, 3])
                    ->where('hauptmenuspalte', $colInt)
                    ->where('id', '!=', $instructionId)
                    ->update([
                        'hauptmenuspalte' => $colInt + 10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            foreach ($colsToShift as $col) {
                $this->normalizeMenuColumn(((int)$col) + 10);
            }

            // Betroffene Spalten sauber sortieren.
            $this->normalizeMenuColumn(10);
            if ($fromCol !== 10 && $fromCol > 0) {
                $this->normalizeMenuColumn($fromCol);
            }

            return Redirect()->back()->with('success', 'Der Menüpunkt wurde auf die Top-Position (Spalte 10 / Position 10) gesetzt.');
        }

        if ((int)$instruction->hauptmenu === 2) {

            $fromCol = (int)$instruction->hauptmenuspalte;

            $colsToShift = Instruction::where('hauptmenuspalte', '=', $fromCol)
               ->pluck('id')
               ->values();

            foreach ($colsToShift as $col) {
                $colInt = (int)$col;

                Instruction::where('id', $colInt)
                     ->update([
                        'hauptmenuspalte' => 10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            // Alle anderen Menüspalten links der Quellspalte um +10 nach rechts schieben.
            $this->shiftMenuColumnsLeftOf($fromCol, $colsToShift->all(), 10);

            return Redirect()->back()->with('success', 'Das Hauptmenu wurde in die erste Menüspalte verschoben (Spalte 10) und alle anderen Menüspalten wurden um +10 erhöht.');
        }

        if ($instruction->hauptmenu == 3) {

            $fromCol = (int)$instruction->hauptmenuspalte;
            $fromPosition=$instruction->position;

            $colsToShift = Instruction::where('hauptmenuspalte', '=', $fromCol)
                ->where('hauptmenu', 3)
                ->where('id', '!=', $instructionId)
                ->pluck('id')
                ->values();

            Instruction::where('id', $instructionId)
                    ->update([
                        'position' => 10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);

            $this->shiftHm3PositionsBefore((int)$fromPosition, $colsToShift->all(), 10);

            return Redirect()->back()->with('success', 'Der Unterpunkt wurde auf die Top-Position (Spalte 10 / Position 10) gesetzt.');
        }

        return Redirect()->back()->with('success', 'Die Informationsseite wurde zur Top Position verschoben.');
    }

    public function top($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        $fromhauptmenuspalte = (int)$instruction->hauptmenuspalte;
        $fromPosition = (int)$instruction->position;

        if ($instruction->hauptmenu == 1 || $instruction->hauptmenu == 2) {

            $colsToShiftNew = Instruction::whereIn('hauptmenu', [1, 2, 3])
                ->where('hauptmenuspalte', $fromhauptmenuspalte)
                ->pluck('id')
                ->values();

            foreach ($colsToShiftNew as $col) {
                Instruction::where('id', $col)
                    ->update([
                        'hauptmenuspalte' => $fromhauptmenuspalte -10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            $colsToShift = Instruction::whereIn('hauptmenu', [1, 2, 3])
                ->whereNotIn('id', $colsToShiftNew)
                ->where('hauptmenuspalte', $fromhauptmenuspalte - 10)
                ->pluck('id')
                ->values();

            foreach ($colsToShift as $col) {
                Instruction::where('id', $col)
                    ->update([
                        'hauptmenuspalte' => $fromhauptmenuspalte,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            return Redirect()->back()->with('success', 'Der Unterpunkt wurde auf die Top-Position (Spalte 10 / Position 10) gesetzt.');
        }

        if ($instruction->hauptmenu == 3) {

            Instruction::where('id', $instructionId)
                ->update([
                    'position' => $fromPosition - 10,
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now(),
                ]);

            $colsToShift = Instruction::where('hauptmenuspalte', $fromhauptmenuspalte)
                ->where('position', $fromPosition - 10)
                ->where('hauptmenu', 3)
                ->where('id', '!=', $instructionId)
                ->pluck('id')
                ->values();

            foreach ($colsToShift as $col) {
                Instruction::where('id', $col)
                    ->update([
                        'position' => $fromPosition,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            return Redirect()->back()->with('success', 'Der Unterpunkt wurde auf die Top-Position (Spalte 10 / Position 10) gesetzt.');
        }

        return Redirect()->back()->with('warning', 'Es konnte nichts verschoben werden.');
    }

    public function down($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        // Wenn ein Sammelmenü-Container verschoben wird, muss die ganze Gruppe mit.
        if ((int)$instruction->hauptmenu === 2) {
            $this->moveMenuColumn((int)$instruction->hauptmenuspalte, +1);
            return Redirect()->back()->with('success', 'Das Hauptmenu wurde inkl. Unterpunkten nach unten verschoben.');
        }

        $positionNew = $instruction->position + 11;
        $menulevel = $instruction->hauptmenuspalte;
        Instruction::findOrFail($instructionId)->update([
            'position' => $positionNew,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);

        $this->normalizeMenuColumn($menulevel);
        return Redirect()->back()->with('success', 'Die Informationsseite wurde eine Position nach unten verschoben.');
    }

    public function maxdown($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        $menulevel = (int)$instruction->hauptmenuspalte;

        // Wenn ein Sammelmenü-Container verschoben wird, muss die ganze Gruppe mit.
        if ((int)$instruction->hauptmenu === 2) {
            $this->moveMenuColumn($menulevel, +1);
            return Redirect()->back()->with('success', 'Das Hauptmenu wurde inkl. Unterpunkten nach unten verschoben.');
        }

        // Sonderfall: Top-Level-Menüpunkt (hauptmenu=1) soll beim "maxdown"
        // in eine NEUE Spalte rechts neben der aktuell höchsten Menüspalte verschoben werden.
        // Wichtig: Die höchste Spalte wird über hauptmenu IN (1,2,3) bestimmt (nicht nur HM=2).
        if ((int)$instruction->hauptmenu === 1) {
            $oldColumn = $menulevel;

            // Zielspalte = (max hauptmenuspalte über HM=1..3) + 10.
            // Beispiel: max=60 => Ziel=70.
            // Fallback: Wenn es noch keine Menüspalte gibt, bleibt der Eintrag in seiner aktuellen Spalte.
            $lastMenuColumn = (int)(Instruction::whereIn('hauptmenu', [1, 2, 3])
                ->where('hauptmenuspalte', '>', 0)
                ->max('hauptmenuspalte') ?? 0);

            $targetColumn = $lastMenuColumn > 0 ? ($lastMenuColumn + 10) : $oldColumn;

            // Neue Spalte startet immer bei Position 10.
            $positionNew = 10;

            Instruction::findOrFail($instructionId)->update([
                'hauptmenuspalte' => $targetColumn,
                'position' => $positionNew,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);

            // Nach dem Umhängen: Menüspalten insgesamt komprimieren,
            // damit eine freigewordene hauptmenuspalte entfernt wird
            // und alle nachfolgenden Spalten um 10 "nach links" rutschen (HM=1..3).
            $this->normalizeAllMenuColumns();

            return Redirect()->back()->with('success', 'Die Informationsseite wurde in das letzte Hauptmenü-Cluster verschoben.');
        }

        $last = Instruction::where('hauptmenuspalte', $menulevel)
            ->orderby('position', 'desc')
            ->first();
        $positionNew = ($last?->position ?? 0) + 10;

        Instruction::findOrFail($instructionId)->update([
            'position' => $positionNew,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);

        $this->normalizeMenuColumn($menulevel);
        return Redirect()->back()->with('success', 'Die Informationsseite wurde zur letzten Position verschoben.');
    }

    public function menuNew($instructionId)
    {
            Instruction::findOrFail($instructionId)->update([
                'hauptmenu'       => 2,
                'position'            => 0,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);

        return Redirect()->back()->with('success', 'Zu ein Menu-Sammler erstellt.');
    }

    public function menuMinus($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        $hauptmenuspalteFilter = $instruction->hauptmenuspalte;

        $instructions = Instruction::where('position', '>', 0)
            ->where('hauptmenuspalte', $hauptmenuspalteFilter)
            ->orderby('position')
            ->get();

        $instructionCount=($instructions->count()-1)*10+$hauptmenuspalteFilter;

        $instructionVerschiebens = Instruction::where('hauptmenuspalte', '>' , $hauptmenuspalteFilter)
            ->orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        foreach ($instructionVerschiebens as $instructionVerschieben) {
            Instruction::findOrFail($instructionVerschieben->id)->update([
                'hauptmenuspalte' => $instructionVerschieben->hauptmenuspalte+$instructionCount,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
        }

        foreach ($instructions as $instruction) {
            $hauptmenuspalteFilter=$hauptmenuspalteFilter+10;
            Instruction::findOrFail($instruction->id)->update([
                'hauptmenu'       => 1,
                'hauptmenuspalte' => $hauptmenuspalteFilter,
                'position'        => 10,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
        }

        Instruction::findOrFail($instructionId)->update([
            'hauptmenu'     => 1,
            'position'      => 10,
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);

        // Nach dem Auflösen: Spalten komprimieren (Lücken entfernen) und Positionen sauber halten.
        $this->normalizeAllMenuColumns();
        return Redirect()->back()->with('success', 'Der Posten wurde zur letzten Position verschoben.');
    }

    /**
     * Container (hauptmenu=2) zu normalem Hauptmenüpunkt (hauptmenu=1) umwandeln,
     * aber NUR wenn es in dieser Spalte keine Unterpunkte (hauptmenu=3) gibt.
     */
    public function toMainLinkIfNoChildren($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);
        $fromCol = (int)$instruction->hauptmenuspalte;

        if ((int)$instruction->hauptmenu !== 1 && (int)$instruction->hauptmenu !== 2 ) {
            return Redirect()->back()->with('warning', 'Aktion nur für Menü-Container möglich.');
        }

        $col = (int)$instruction->hauptmenuspalte;
        $hasChildren = Instruction::where('hauptmenuspalte', $col)
            ->where('hauptmenu', 3)
            ->exists();

        if ($hasChildren) {
            return Redirect()->back()->with('warning', 'Der Container hat Unterpunkte und kann nicht zu einem normalen Hauptmenüpunkt umgewandelt werden.');
        }

        Instruction::findOrFail($instructionId)->update([
            'hauptmenu' => 0,
            'hauptmenuspalte' => 0,
            'position' => 0,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        $updateCols= Instruction::where('hauptmenuspalte', '>', $fromCol)
            ->where('hauptmenu', '!=', 0)
            ->get();

        foreach ($updateCols as $col) {

            Instruction::where('id', $col->id)
                ->update([
                    'hauptmenuspalte' => $col->hauptmenuspalte - 10,
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now(),
                ]);
        }

        return Redirect()->back()->with('success', 'Der Container wurde zu einem Hauptmenüpunkt umgewandelt.');
    }

    public function menuPlus($instructionId)
    {
        // NEU: Pfeil rechts bei einem normalen Hauptmenüpunkt (HM=1)
        // => in die vorherige Menüspalte verschieben (hauptmenuspalte - 10)
        // und dort ans Ende einsortieren (position = max(position) + 10).
        $clicked = Instruction::findOrFail($instructionId);
        $fromCol = (int)$clicked->hauptmenuspalte;

        if ((int)$clicked->hauptmenu === 1) {

            // Ziel ist der nächste Container (HM=2) "links" vom aktuellen Punkt,
            // also die nächst niedrigere hauptmenuspalte mit hauptmenu=2.
            // Beispiel: von Spalte 40 -> Container in Spalte 20 ermitteln und dort unten anhängen.
            // Fallback: Wenn es keinen Container links gibt, versuche Spalte -10.
            $prevContainerCol = Instruction::where('hauptmenu', 2)
                ->where('hauptmenuspalte', '<', $fromCol)
                ->orderBy('hauptmenuspalte', 'desc')
                ->value('hauptmenuspalte');

            $targetCol = $prevContainerCol !== null ? (int)$prevContainerCol : ($fromCol - 10);

            if ($targetCol < 10) {
                return Redirect()->back()->with('warning', 'Der Menüpunkt kann nicht weiter verschoben werden.');
            }

            $lastPos = (int)(Instruction::where('hauptmenuspalte', $targetCol)->max('position') ?? 0);
            $newPos = $lastPos + 10;

            $clicked->update([
                'hauptmenu' => 3,
                'hauptmenuspalte' => $targetCol,
                'position' => $newPos,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            $updateCols= Instruction::where('hauptmenuspalte', '>', $fromCol)
                ->where('hauptmenu', '!=', 0)
                ->get();

            foreach ($updateCols as $col) {

                Instruction::where('id', $col->id)
                    ->update([
                        'hauptmenuspalte' => $col->hauptmenuspalte - 10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            return Redirect()->back()->with('success', 'Der Menüpunkt wurde als Unterpunkt einsortiert.');
        }

        $instructions = Instruction::orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        $first=0;
        $firstPosition=0;
        $positionAktuel=10;
        $positionOld=10;
        $hauptmenuspalteNew=10;
        foreach ($instructions as $instruction) {
            if($first==0)
            {
                if($instruction->hauptmenu=="3") {
                    $positionAktuel=$positionAktuel+10;
                }
                if($instruction->hauptmenu=="2"){
                    $hauptmenuspalteNew=$instruction->hauptmenuspalte;
                    $positionAktuel=0;
                }
            }

            if($first==1){
                $hauptmenuspalteNew=$instruction->hauptmenuspalte-10;
                Instruction::findOrFail($instruction->id)->update([
                    'hauptmenuspalte' => $hauptmenuspalteNew,
                    'bearbeiter_id'   => Auth::id(),
                    'updated_at'      => Carbon::now()
                ]);
            }

            if($instructionId==$instruction->id) {
                    $first = 1;
                    Instruction::findOrFail($instruction->id)->update([
                        'hauptmenu'       => 3,
                        'hauptmenuspalte' => $hauptmenuspalteNew,
                        'position'        => $positionAktuel+10,
                        'bearbeiter_id'   => Auth::id(),
                        'updated_at'      => Carbon::now()
                    ]);
            }
        }
        // Nach dem Umhängen in ein Dropdown: Spalten wieder komprimieren.
        $this->normalizeAllMenuColumns();
        return Redirect()->back()->with('success', 'Die Informationsseite zum Sammelmenu umgewandelt.');
    }

    public function menuDelete($instructionId)
    {
        // Sonderfall: Pfeil links auf einem Unterpunkt (hauptmenu=3)
        // => soll zu einem normalen Hauptmenüpunkt (hauptmenu=1) werden.
        // Ziel: neue Spalte rechts neben der aktuellen Spalte (hauptmenuspalte + 10).
        // Dafür müssen alle bestehenden Menüspalten ab dieser Zielspalte um +10 verschoben werden.
        $instruction = Instruction::findOrFail($instructionId);
        if ((int)$instruction->hauptmenu === 3) {
            $fromCol = (int)$instruction->hauptmenuspalte;
            $targetCol = $fromCol + 10;

            // Platz schaffen: alle Menü-Einträge (HM=1..3) ab targetCol um +10 verschieben.
            // Descending, um Kollisionen zu vermeiden.
            $colsToShift = Instruction::whereIn('hauptmenu', [1, 2, 3])
                ->where('hauptmenuspalte', '>=', $targetCol)
                ->pluck('hauptmenuspalte')
                ->unique()
                ->sortDesc()
                ->values();

            foreach ($colsToShift as $col) {
                $colInt = (int)$col;
                Instruction::whereIn('hauptmenu', [1, 2, 3])
                    ->where('hauptmenuspalte', $colInt)
                    ->update([
                        'hauptmenuspalte' => $colInt + 10,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            // Den Unterpunkt in die neue Spalte umhängen und als HM=1 führen.
            Instruction::findOrFail($instructionId)->update([
                'hauptmenu' => 1,
                'hauptmenuspalte' => $targetCol,
                'position' => 10,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            // Stabilisieren: Positionen der Quell- und Zielspalte, danach Gesamtstruktur komprimieren.
            $this->normalizeMenuColumn($fromCol);
            $this->normalizeMenuColumn($targetCol);
            $this->normalizeAllMenuColumns();

            return Redirect()->back()->with('success', 'Der Unterpunkt wurde zu einem Hauptmenüpunkt umgewandelt.');
        }

        $positionNew=0;
        $instructions=Instruction::where('hauptmenu' , 0)
            ->orderby('position')
            ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        $instruction = Instruction::findOrFail($instructionId);
        $hauptmenuspalteFilter = $instruction->hauptmenuspalte;

        Instruction::findOrFail($instructionId)->update([
            'hauptmenu'       => 0,
            'hauptmenuspalte' => 0,
            'position'        => $positionNew,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        // Rest der betroffenen Menüspalte wieder sauber sortieren.
        $this->normalizeMenuColumn((int)$hauptmenuspalteFilter);
        // Menüspalten insgesamt komprimieren (Lücken entfernen) und Info-Seiten-Liste normalisieren.
        $this->normalizeAllMenuColumns();
        $this->normalizeInfoPagesWithoutMenu();
        return Redirect()->back()->with('success', 'Die Informationsseite aus dem Menu entfernt.');
    }

    /**
     * Macht eine Informationsseite (typischerweise hauptmenu=0) zu einem Top-Level-Menüpunkt.
     * Ergebnis: hauptmenu=1, neue hauptmenuspalte (am Ende), position=10.
     */
    public function toMainMenu($instructionId)
    {
        $instruction = Instruction::findOrFail($instructionId);

        // Ziel-Spalte ist immer "am Ende" der aktuellen Menüreihenfolge.
        $maxCol = (int) (Instruction::where('hauptmenuspalte', '>', 0)->max('hauptmenuspalte') ?? 0);
        $newCol = $maxCol > 0 ? $maxCol + 10 : 10;

        $instruction->update([
            'hauptmenu' => 1,
            'hauptmenuspalte' => $newCol,
            'position' => 10,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        // Menüspalte sauber normalisieren.
        $this->normalizeMenuColumn($newCol);
        // Menüspalten ohne Lücken komprimieren.
        $this->normalizeAllMenuColumns();
        // Restliste (hauptmenu=0) nach dem "Entnehmen" wieder in 10er Schritten ordnen.
        $this->normalizeInfoPagesWithoutMenu();

        return Redirect()->back()->with('success', 'Die Informationsseite wurde zum Hauptmenü hinzugefügt.');
    }

    public function MenuDown($instructionId)
    {
        $positionNew=0;
        $instructions=Instruction::where('hauptmenu' , 3)
            ->where('hauptmenuspalte' , 10)
            ->orderby('position')
            ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        Instruction::findOrFail($instructionId)->update([
            'hauptmenu'       => 3,
            'hauptmenuspalte' => 10,
            'position'        => $positionNew,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);
        return Redirect()->back()->with('success', 'Der Informationsseite aktiviert.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Menü-Einträge (alles außer hauptmenu=0) werden wie gewohnt in der Menüreihenfolge gelistet.
        $instructions = Instruction::where('hauptmenu', '!=', 0)
            ->orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        // Informationsseiten ohne Menü werden separat am Ende gelistet.
        $this->normalizeInfoPagesWithoutMenu();
        $infoPagesWithoutMenu = Instruction::where('hauptmenu', 0)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $instructionMaxID = 0;
        if ($instructions->count() > 0) {
            $instructionMaxID = (int)$instructions->max('hauptmenuspalte');
        }

        return view('admin.instruction.index')->with([
            'instructions' => $instructions,
            'infoPagesWithoutMenu' => $infoPagesWithoutMenu,
            'instructionMaxID' => $instructionMaxID,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.instruction.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                'ueberschrift'  => 'required|max:50',
            ]
        );

        $instruction = new Instruction([
                'ueberschrift'             => $request->ueberschrift,
                'hauptmenuspalte'          => 10,
                'systemmenu'               => 0,
                'visible'                  => 1,
                'hauptmenu'                => 1,
                'position'                 => 10,
                'bearbeiter_id'            => Auth::id(),
                'user_id'                  => Auth::id(),
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
            ]
        );

        /* ToDo: mit welchen Werten soll die Felder angelegt werden?
                  'hauptmenuspalte'          => 10,
                  'systemmenu'               => 0,
                  'hauptmenu'                => 1,
                  'position'                 => 10,

         */

        $instruction->save();

        return redirect()->route('instruction.index')
            ->with('success', 'Informationsseite wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function edit($instruction_id)
    {
        $instruction = Instruction::findOrFail($instruction_id);

        return view('admin.instruction.edit',compact('instruction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $instruction_id)
    {
        $instruction = Instruction::findOrFail($instruction_id);

        // Validierung: Überschrift ist nur bei Nicht-Systemseiten zwingend änderbar.
        $rules = [
            'beschreibung' => 'nullable',
        ];
        if ((int) $instruction->systemmenu !== 1) {
            $rules['ueberschrift'] = 'required|max:50';
        } else {
            // Feld kann im Request fehlen oder manipuliert werden – wir ignorieren es serverseitig.
            $rules['ueberschrift'] = 'sometimes|max:50';
        }
        $validated = $request->validate($rules);

        $update = [
            'beschreibung'  => $validated['beschreibung'] ?? null,
            'bearbeiter_id' => Auth::id(),
            'updated_at'  => Carbon::now(),
        ];

        $headlineWasBlocked = false;
        if ((int) $instruction->systemmenu !== 1) {
            $update['ueberschrift'] = $validated['ueberschrift'] ?? $request->ueberschrift;
        } else {
            // Systemseite: Überschrift ist geschützt.
            if ($request->has('ueberschrift') && $request->ueberschrift !== $instruction->ueberschrift) {
                $headlineWasBlocked = true;
            }
        }

        $instruction->update($update);

        $redirect = redirect('/Instruction/alle')->with('success', 'Die Daten wurden geändert.');
        if ($headlineWasBlocked) {
            $redirect->with('warning', 'Systemseite: Die Überschrift ist geschützt und wurde nicht geändert.');
        }
        return $redirect;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function destroy(instruction $instruction)
    {
        //
    }

    public function menulevel1($instructionId , $positionFilter)
    {
        // Historischer Sonderfall: früher wurde hier manuell ein Container/Level-1 umgebaut.
        // Ziel ist aber: wenn ein Hauptmenu-Container verschoben wird, müssen ALLE Unterpunkte
        // (gleiche hauptmenuspalte) mit verschoben werden.
        // Daher nutzen wir jetzt die neue Block-Logik.
        $instruction = Instruction::findOrFail($instructionId);

        // Wenn wir hier landen, ist es immer ein Sammelmenü-Container auf Spalte 10.
        // "MaxTop/Top" bedeutet: diese Spalte als Block nach oben verschieben.
        $this->moveMenuColumn((int)$instruction->hauptmenuspalte, -1);
    }

}
