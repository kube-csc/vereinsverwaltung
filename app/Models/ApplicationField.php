<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationField extends Model
{
    public const TYPES = [
        'text',
        'textarea',
        'number',
        'date',
        'email',
        'select',
        'checkbox',
        'file',
    ];

    protected $fillable = [
        'application_type_id',
        'key',
        'label',
        'type',
        'required',
        'options',
        'help_text',
        'sort_order',
        'aktiv',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'aktiv' => 'boolean',
    ];

    public function applicationType(): BelongsTo
    {
        return $this->belongsTo(ApplicationType::class);
    }
}
