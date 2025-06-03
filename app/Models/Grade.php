<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Grade extends Model
{
    use HasFactory;

    protected $guarded = [];

    /* Relationships */
    public function school(): BelongsTo  { return $this->belongsTo(School::class); }
    public function classes(): HasMany   { return $this->hasMany(SchoolClass::class); }
}
