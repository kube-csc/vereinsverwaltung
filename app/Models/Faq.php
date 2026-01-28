<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'category_sort_order' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('category_sort_order')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
