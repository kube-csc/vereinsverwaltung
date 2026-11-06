<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationType extends Model
{
    protected $fillable = ['name', 'slug', 'beschreibung', 'frist', 'requires_club', 'requires_funding_pot', 'aktiv', 'reviewer_note'];

    protected $casts = [
        'frist' => 'date',
        'requires_club' => 'boolean',
        'aktiv' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ApplicationField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function fundingPots(): BelongsToMany
    {
        return $this->belongsToMany(FundingPot::class, 'application_type_funding_pot')->withTimestamps();
    }

    /**
     * Gibt die Gründe zurück, warum diese Antragsart nicht bearbeitet werden darf.
     * Gibt null zurück, wenn die Bearbeitung erlaubt ist.
     * Wenn beide Gründe zutreffen, werden beide Texte kombiniert zurückgegeben.
     */
    public function lockReason(): ?string
    {
        $reasons = [];

        // Wenn ein zugeordneter Fördertopf eröffnet (nicht geschlossen) ist,
        // darf die Antragsart nicht bearbeitet werden.
        if ($this->fundingPots()->where('geschlossen', false)->exists()) {
            $reasons[] = 'ein zugeordneter Fördertopf noch geöffnet ist';
        }

        // Wenn nicht-endgültige Anträge existieren
        if ($this->applications()
            ->whereNotIn('status', [Application::STATUS_REJECTED])
            ->where(function ($query) {
                $query->where('status', '!=', Application::STATUS_APPROVED)
                    ->orWhere(function ($q) {
                        $q->where('status', Application::STATUS_APPROVED)
                            ->whereNotNull('funding_pot_id')
                            ->whereNull('final_amount');
                    });
            })
            ->whereNotNull('submitted_at')
            ->exists()) {
            $reasons[] = 'noch nicht abgeschlossene Anträge vorhanden sind';
        }

        if (empty($reasons)) {
            return null;
        }

        if (count($reasons) === 1) {
            return 'Diese Antragsart kann nicht bearbeitet werden, solange ' . $reasons[0] . '.';
        }

        // Beide Gründe zutreffen – kombinieren
        return 'Diese Antragsart kann nicht bearbeitet werden, solange ' . implode(' und ', $reasons) . '.';
    }

    /**
     * Prüft, ob diese Antragsart noch bearbeitet werden darf.
     * Bearbeitung ist nicht erlaubt, wenn:
     * 1. Mindestens ein Antrag existiert, der noch nicht endgültig abgeschlossen ist.
     * 2. Mindestens ein zugeordneter Fördertopf aktuell eröffnet (nicht geschlossen) ist.
     *
     * Ein Antrag gilt als endgültig abgeschlossen, wenn:
     * - Status "abgelehnt" ist, oder
     * - Status "bewilligt" ist UND (kein Fördertopf ODER final_amount gesetzt).
     *
     * Anträge ohne Fördertopf (funding_pot_id IS NULL) sind nach Bewilligung
     * sofort endgültig, da kein final_amount erforderlich ist.
     * Anträge mit Fördertopf sind erst nach Endabrechnung (final_amount gesetzt)
     * endgültig.
     */
    public function canEdit(): bool
    {
        return $this->lockReason() === null;
    }
}
