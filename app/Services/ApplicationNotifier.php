<?php

namespace App\Services;

use App\Mail\ApplicationNotificationMail;
use App\Models\Application;
use App\Models\FundingPot;
use App\Models\NoticeTemplate;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** Versand der E-Mail-Benachrichtigungen und Erzeugung der Bescheid-PDFs. */
class ApplicationNotifier
{
    /** Vorlagentyp des Bescheids, null wenn für den Status noch kein Bescheid existiert. */
    public static function noticeType(Application $application): ?string
    {
        $hasPot = $application->funding_pot_id !== null;

        return match ($application->status) {
            Application::STATUS_APPROVED => $hasPot
                ? ($application->final_amount !== null ? NoticeTemplate::SETTLED : NoticeTemplate::APPROVED)
                : NoticeTemplate::APPROVED_NO_POT,
            Application::STATUS_REJECTED => $hasPot
                ? NoticeTemplate::REJECTED
                : NoticeTemplate::REJECTED_NO_POT,
            default => null,
        };
    }

    public function pdf(Application $application): string
    {
        $type = self::noticeType($application);
        abort_if($type === null, 404);

        $application->loadMissing('decider', 'applicationType');
        $notice = NoticeTemplate::forType($type)->render($application);

        $deciderName = '';
        $decider = $application->decider;
        if ($decider !== null) {
            $deciderName = trim(($decider->vorname ?? '') . ' ' . ($decider->nachname ?? '')) ?: (string) ($decider->name ?? '');
        }

        $html = view('antraege.bescheid-pdf', [
            'notice' => $notice,
            'application' => $application,
            'verein' => config('app.verein_name'),
            'deciderName' => $deciderName,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        // PDF nur speichern, wenn der Antrag endgültig entschieden ist
        if ($this->isFinalDecision($application)) {
            $this->savePdf($application, $pdfContent);
        }

        return $pdfContent;
    }

    /**
     * Prüft, ob der Antrag endgültig entschieden ist und das Bescheid-PDF gespeichert werden darf.
     * - Anträge ohne Fördertopf: Bei bewilligt oder abgelehnt ist die Entscheidung endgültig.
     * - Anträge mit Fördertopf: Erst nach Endabrechnung (final_amount gesetzt) ist die Bewilligung endgültig.
     *   Eine Ablehnung ist auch bei Fördertopf-Anträgen sofort endgültig.
     */
    private function isFinalDecision(Application $application): bool
    {
        $hasPot = $application->funding_pot_id !== null;

        // Ablehnung ist immer endgültig
        if ($application->status === Application::STATUS_REJECTED) {
            return true;
        }

        // Bewilligung ohne Fördertopf ist sofort endgültig
        if (!$hasPot && $application->status === Application::STATUS_APPROVED) {
            return true;
        }

        // Bewilligung mit Fördertopf ist erst nach Endabrechnung endgültig
        if ($hasPot && $application->status === Application::STATUS_APPROVED && $application->final_amount !== null) {
            return true;
        }

        return false;
    }

    /**
     * Speichert das Bescheid-PDF im Storage-Verzeichnis und hinterlegt den Pfad in der Datenbank.
     * Wenn bereits ein PDF gespeichert ist, wird es überschrieben — z. B. nach Endabrechnung
     * wird das endgültige PDF das vorläufige ersetzen.
     */
    private function savePdf(Application $application, string $pdfContent): void
    {
        $directory = 'local/bescheide/' . $application->id;
        $filename = 'bescheid_' . $application->id . '_' . now()->format('Y-m-d') . '_' . $application->status . '.pdf';
        $path = $directory . '/' . $filename;

        // Falls bereits ein PDF existiert, löschen
        if (!empty($application->bescheid_pdf_path)) {
            Storage::disk('local')->delete($application->bescheid_pdf_path);
        }

        Storage::disk('local')->put($path, $pdfContent);

        $application->bescheid_pdf_path = $path;
        $application->save();
    }

    public function question(Application $application, string $question): void
    {
        $this->send($application, 'Rückfrage zu Ihrem Antrag #' . $application->id, [
            'zu Ihrem Antrag #' . $application->id . ' hat das Gremium eine Rückfrage:',
            $question,
            'Bitte beantworten Sie die Rückfrage unter „Meine Anträge“.',
        ]);
    }

    public function decision(Application $application): void
    {
        $application->refresh();
        $application->loadMissing('applicationType');
        $type = self::noticeType($application);
        if ($type === null) {
            return;
        }

        $approved = $application->status === Application::STATUS_APPROVED;
        $hasPot = $application->funding_pot_id !== null;
        $lines = [
            $approved
                ? 'Ihr Antrag #' . $application->id . ' wurde bewilligt.'
                : 'Ihr Antrag #' . $application->id . ' wurde abgelehnt.',
        ];

        // Eingegebene Antragsdaten hinzufügen
        $answers = $application->formattedAnswers();
        if (!empty($answers)) {
            $lines[] = 'Eingegebene Antragsdaten:';
            foreach ($answers as $answerLine) {
                $lines[] = '- ' . $answerLine;
            }
        }

        // Prüfaufgabe (reviewer_note) hinzufügen, falls vorhanden
        if (!empty($application->applicationType->reviewer_note)) {
            $lines[] = 'Prüfaufgabe: ' . $application->applicationType->reviewer_note;
        }

        // Prüfer-Antworten hinzufügen, falls vorhanden
        $reviewerAnswerLines = $application->formattedReviewerAnswers();
        if (!empty($reviewerAnswerLines)) {
            $lines[] = 'Prüfer-Antworten:';
            foreach ($reviewerAnswerLines as $answerLine) {
                $lines[] = '- ' . $answerLine;
            }
        }

        $lines[] = 'Den Bescheid finden Sie im Anhang und unter „Meine Anträge“.';

        if ($approved && $hasPot) {
            $lines = array_merge($lines, $this->bankLines($application));
        }

        $this->send($application, ($approved ? 'Bewilligung' : 'Ablehnung') . ' Ihres Antrags #' . $application->id, $lines, true);
    }

    /** Nach der Endabrechnung: endgültiger Betrag und Hinweis zur Bankverbindung. */
    public function settled(FundingPot $pot): void
    {
        $applications = $pot->applications()
            ->where('status', Application::STATUS_APPROVED)
            ->whereNotNull('final_amount')
            ->with('applicationType')
            ->get();

        foreach ($applications as $application) {
            $lines = [
                'der Fördertopf „' . $pot->name . '“ wurde abgerechnet. Der endgültige Förderbetrag für Ihren Antrag #' . $application->id
                    . ' beträgt ' . number_format((float) $application->final_amount, 2, ',', '.') . ' €.',
            ];

            // Eingegebene Antragsdaten hinzufügen
            $answers = $application->formattedAnswers();
            if (!empty($answers)) {
                $lines[] = 'Eingegebene Antragsdaten:';
                foreach ($answers as $answerLine) {
                    $lines[] = '- ' . $answerLine;
                }
            }

            // Prüfaufgabe (reviewer_note) hinzufügen, falls vorhanden
            if (!empty($application->applicationType->reviewer_note)) {
                $lines[] = 'Prüfaufgabe: ' . $application->applicationType->reviewer_note;
            }

            // Prüfer-Antworten hinzufügen, falls vorhanden
            $reviewerAnswerLines = $application->formattedReviewerAnswers();
            if (!empty($reviewerAnswerLines)) {
                $lines[] = 'Prüfer-Antworten:';
                foreach ($reviewerAnswerLines as $answerLine) {
                    $lines[] = '- ' . $answerLine;
                }
            }

            $this->send($application, 'Endabrechnung: Förderbetrag zu Ihrem Antrag #' . $application->id, array_merge($lines, $this->bankLines($application)), true);
        }
    }

    /** Hinweis zur Bankverbindung für die Auszahlung, IBAN teilweise unkenntlich. */
    private function bankLines(Application $application): array
    {
        $account = $application->payoutAccountData();

        if ($account === null) {
            return ['Für Ihren Antrag ist noch keine Bankverbindung hinterlegt. Bitte tragen Sie Ihre Kontonummer (IBAN) unter „Bankkonten“ ein und ordnen Sie das Konto unter „Meine Anträge“ Ihrem Antrag zu, damit wir die Förderung überweisen können.'];
        }

        $iban = (string) ($account['iban'] ?? '');

        return [
            'Wir überweisen auf das Konto von ' . ($account['kontoinhaber'] ?? '') . ' (IBAN ' . substr($iban, 0, 4) . ' •••• ' . substr($iban, -4) . ').',
            'Bitte überprüfen Sie diese Bankverbindung und passen Sie sie bei Bedarf unter „Meine Anträge“ an, bevor die Überweisung erstellt wird.',
        ];
    }

    /** Ein Fehler beim Mailversand darf die fachliche Aktion nie verhindern. */
    private function send(Application $application, string $subject, array $lines, bool $withPdf = false): void
    {
        try {
            $application->loadMissing('user', 'decider', 'applicationType');
            $user = $application->user;
            if (! $user || blank($user->email)) {
                return;
            }

            $pdf = $withPdf
                ? ['name' => 'Bescheid-Antrag-' . $application->id . '.pdf', 'data' => $this->pdf($application)]
                : null;

            $name = trim(($user->vorname ?? '') . ' ' . ($user->nachname ?? '')) ?: (string) $user->name;

            $decider = $application->decider;
            $deciderName = trim(($decider?->vorname ?? '') . ' ' . ($decider?->nachname ?? '')) ?: (string) $decider?->name;

            Mail::to($user->email)->send(new ApplicationNotificationMail($subject, $name, $lines, $pdf, $deciderName));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
