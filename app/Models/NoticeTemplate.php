<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeTemplate extends Model
{
    public const APPROVED = 'bewilligt';
    public const REJECTED = 'abgelehnt';
    public const SETTLED = 'endabrechnung';
    public const APPROVED_NO_POT = 'bewilligt_kein_topf';
    public const REJECTED_NO_POT = 'abgelehnt_kein_topf';

    protected $fillable = ['type', 'titel', 'text'];

    public const PLACEHOLDERS = [
        '{name}' => 'Vor- und Nachname der antragstellenden Person',
        '{vorname}' => 'Vorname der antragstellenden Person',
        '{nachname}' => 'Nachname der antragstellenden Person',
        '{antrag}' => 'Antragsnummer',
        '{antragsart}' => 'Name der Antragsart',
        '{foerdertopf}' => 'Name des Fördertopfs',
        '{verein}' => 'Name des Vereins',
        '{betrag}' => 'Förderbetrag (endgültig, sonst bewilligt)',
        '{begruendung}' => 'Begründung der Entscheidung',
        '{datum}' => 'Datum der Entscheidung bzw. des Bescheids',
        '{antragsdaten}' => 'Im Antrag eingetragene Daten (Label: Wert pro Zeile)',
    ];

    public static function labels(): array
    {
        return [
            self::APPROVED => 'Bewilligung',
            self::REJECTED => 'Ablehnung',
            self::SETTLED => 'Bewilligung nach Endabrechnung (endgültiger Betrag)',
            self::APPROVED_NO_POT => 'Bewilligung (ohne Fördertopf)',
            self::REJECTED_NO_POT => 'Ablehnung (ohne Fördertopf)',
        ];
    }

    public static function defaults(): array
    {
        return [
            self::APPROVED => [
                'titel' => 'Bewilligungsbescheid',
                'text' => "Sehr geehrte/r {vorname} {nachname},\n\nIhr Antrag #{antrag} ({antragsart}) wurde bewilligt. Der vorläufig bewilligte Förderbetrag beträgt {betrag}.\n\nBegründung: {begruendung}\n\nDer endgültige Betrag steht erst nach der Endabrechnung des Fördertopfs {foerdertopf} fest. Wir informieren Sie darüber gesondert.",
            ],
            self::REJECTED => [
                'titel' => 'Ablehnungsbescheid',
                'text' => "Sehr geehrte/r {vorname} {nachname},\n\nIhr Antrag #{antrag} ({antragsart}) konnte leider nicht berücksichtigt werden.\n\nBegründung: {begruendung}",
            ],
            self::SETTLED => [
                'titel' => 'Bewilligungsbescheid (endgültig)',
                'text' => "Sehr geehrte/r {vorname} {nachname},\n\nnach der Endabrechnung des Fördertopfs {foerdertopf} steht der endgültige Förderbetrag für Ihren Antrag #{antrag} ({antragsart}) fest: {betrag}.",
            ],
            self::APPROVED_NO_POT => [
                'titel' => 'Bescheid',
                'text' => "Sehr geehrte/r {vorname} {nachname},\n\nIhr Antrag #{antrag} ({antragsart}) wurde bewilligt.\n\nBegründung: {begruendung}",
            ],
            self::REJECTED_NO_POT => [
                'titel' => 'Bescheid',
                'text' => "Sehr geehrte/r {vorname} {nachname},\n\nIhr Antrag #{antrag} ({antragsart}) konnte leider nicht berücksichtigt werden.\n\nBegründung: {begruendung}",
            ],
        ];
    }

    /** Gespeicherte Vorlage, sonst die Standardvorlage (nicht gespeichert). */
    public static function forType(string $type): self
    {
        return static::where('type', $type)->first()
            ?? new static(['type' => $type] + (static::defaults()[$type] ?? ['titel' => '', 'text' => '']));
    }

    /** Ersetzt die Platzhalter und liefert Titel und Text des Bescheids. */
    public function render(Application $application): array
    {
        $application->loadMissing(['user', 'applicationType', 'fundingPot', 'club']);
        $user = $application->user;
        $amount = $application->final_amount ?? $application->approved_amount;
        $answersText = $application->formattedAnswersText();

        $fullName = trim(($user?->vorname ?? '') . ' ' . ($user?->nachname ?? '')) ?: (string) $user?->name;
        $values = [
            '{name}' => $fullName,
            '{vorname}' => (string) $user?->vorname,
            '{nachname}' => (string) $user?->nachname,
            '{antrag}' => (string) $application->id,
            '{antragsart}' => (string) $application->applicationType?->name,
            '{foerdertopf}' => (string) $application->fundingPot?->name,
            '{verein}' => (string) $application->club?->clubname,
            '{betrag}' => $amount !== null ? number_format((float) $amount, 2, ',', '.') . ' €' : '-',
            '{begruendung}' => (string) $application->decision_reason,
            '{datum}' => ($application->decided_at ?? now())->format('d.m.Y'),
            '{antragsdaten}' => $answersText,
        ];

        return [
            'titel' => strtr($this->titel, $values),
            'text' => strtr($this->text, $values),
        ];
    }
}
