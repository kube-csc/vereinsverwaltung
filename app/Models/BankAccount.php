<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = ['kontoinhaber', 'bank_name', 'iban', 'bic'];

    protected $hidden = ['iban', 'bic'];

    protected $casts = [
        'iban' => 'encrypted',
        'bic' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** IBAN in Vierergruppen, z. B. „DE89 3704 0044 0532 0130 00“. */
    public function formattedIban(): string
    {
        return trim(chunk_split($this->iban, 4, ' '));
    }

    /** Maskierte Darstellung, nur die letzten vier Stellen sichtbar. */
    public function maskedIban(): string
    {
        return substr($this->iban, 0, 4) . ' •••• •••• ' . substr($this->iban, -4);
    }

    /** Prüft Format und Prüfsumme einer IBAN (ISO 13616, Modulo 97). */
    public static function isValidIban(string $iban): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));

        if (! preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{11,30}$/', $iban)) {
            return false;
        }

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        $remainder = 0;
        foreach (str_split($numeric, 7) as $part) {
            $remainder = (int) ($remainder . $part) % 97;
        }

        return $remainder === 1;
    }
}
