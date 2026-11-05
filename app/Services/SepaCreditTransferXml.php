<?php

namespace App\Services;

use DateTimeInterface;
use DomainException;
use DOMDocument;
use DOMElement;

/** Erzeugt SEPA-Sammelüberweisungen im Format ISO 20022 pain.001.001.03. */
class SepaCreditTransferXml
{
    private const NAMESPACE = 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03';

    /**
     * @param array<int, array{end_to_end: string, name: string, iban: string, bic: ?string, amount_cent: int, purpose: string}> $payments
     */
    public function build(
        string $debtorName,
        string $debtorIban,
        ?string $debtorBic,
        array $payments,
        DateTimeInterface $executionDate,
        string $messageId
    ): string {
        if ($payments === []) {
            throw new DomainException('Keine Zahlungen für die Überweisungsdatei vorhanden.');
        }
        if (trim($debtorName) === '' || trim($debtorIban) === '') {
            throw new DomainException('Das Auftraggeberkonto ist nicht hinterlegt. Bitte im Fördertopf eine Bankverbindung eintragen.');
        }

        $count = count($payments);
        $totalCent = array_sum(array_column($payments, 'amount_cent'));
        $dbtName = self::text($debtorName, 70);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $root = $doc->createElementNS(self::NAMESPACE, 'Document');
        $doc->appendChild($root);
        $init = $this->add($doc, $root, 'CstmrCdtTrfInitn');

        $header = $this->add($doc, $init, 'GrpHdr');
        $this->add($doc, $header, 'MsgId', $messageId);
        $this->add($doc, $header, 'CreDtTm', now()->format('Y-m-d\TH:i:s'));
        $this->add($doc, $header, 'NbOfTxs', (string) $count);
        $this->add($doc, $header, 'CtrlSum', self::amount($totalCent));
        $this->add($doc, $this->add($doc, $header, 'InitgPty'), 'Nm', $dbtName);

        $info = $this->add($doc, $init, 'PmtInf');
        $this->add($doc, $info, 'PmtInfId', $messageId);
        $this->add($doc, $info, 'PmtMtd', 'TRF');
        $this->add($doc, $info, 'BtchBookg', 'true');
        $this->add($doc, $info, 'NbOfTxs', (string) $count);
        $this->add($doc, $info, 'CtrlSum', self::amount($totalCent));
        $this->add($doc, $this->add($doc, $this->add($doc, $info, 'PmtTpInf'), 'SvcLvl'), 'Cd', 'SEPA');
        $this->add($doc, $info, 'ReqdExctnDt', $executionDate->format('Y-m-d'));
        $this->add($doc, $this->add($doc, $info, 'Dbtr'), 'Nm', $dbtName);
        $this->add($doc, $this->add($doc, $this->add($doc, $info, 'DbtrAcct'), 'Id'), 'IBAN', self::iban($debtorIban));
        $this->agent($doc, $this->add($doc, $info, 'DbtrAgt'), $debtorBic, true);
        $this->add($doc, $info, 'ChrgBr', 'SLEV');

        foreach ($payments as $payment) {
            $tx = $this->add($doc, $info, 'CdtTrfTxInf');
            $this->add($doc, $this->add($doc, $tx, 'PmtId'), 'EndToEndId', self::text($payment['end_to_end'], 35) ?: 'NOTPROVIDED');
            $amount = $this->add($doc, $this->add($doc, $tx, 'Amt'), 'InstdAmt', self::amount($payment['amount_cent']));
            $amount->setAttribute('Ccy', 'EUR');
            if (filled($payment['bic'] ?? null)) {
                $this->agent($doc, $this->add($doc, $tx, 'CdtrAgt'), $payment['bic'], false);
            }
            $this->add($doc, $this->add($doc, $tx, 'Cdtr'), 'Nm', self::text($payment['name'], 70));
            $this->add($doc, $this->add($doc, $this->add($doc, $tx, 'CdtrAcct'), 'Id'), 'IBAN', self::iban($payment['iban']));
            $this->add($doc, $this->add($doc, $tx, 'RmtInf'), 'Ustrd', self::text($payment['purpose'], 140));
        }

        return $doc->saveXML();
    }

    private function agent(DOMDocument $doc, DOMElement $parent, ?string $bic, bool $required): void
    {
        $institution = $this->add($doc, $parent, 'FinInstnId');
        if (filled($bic)) {
            $this->add($doc, $institution, 'BIC', strtoupper(preg_replace('/\s+/', '', $bic)));
        } elseif ($required) {
            $this->add($doc, $this->add($doc, $institution, 'Othr'), 'Id', 'NOTPROVIDED');
        }
    }

    private function add(DOMDocument $doc, DOMElement $parent, string $name, ?string $value = null): DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, $name);
        if ($value !== null) {
            $element->appendChild($doc->createTextNode($value));
        }
        $parent->appendChild($element);

        return $element;
    }

    public static function amount(int $cent): string
    {
        return number_format($cent / 100, 2, '.', '');
    }

    public static function iban(string $iban): string
    {
        return strtoupper(preg_replace('/\s+/', '', $iban));
    }

    /** Reduziert Text auf den SEPA-Zeichensatz und kürzt ihn. */
    public static function text(string $value, int $max): string
    {
        $value = strtr($value, [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss',
            '–' => '-', '—' => '-', '&' => '+',
        ]);
        $value = preg_replace('/[^a-zA-Z0-9\/\-?:().,\'+ ]/', '', $value);

        return trim(substr(preg_replace('/\s+/', ' ', $value), 0, $max));
    }
}
