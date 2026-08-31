<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reference extends Model
{
    protected $fillable = [
        'type',
        'name',
        'code',
        'short_name',
        'parent_id',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Reference::class, 'parent_id');
    }
}
