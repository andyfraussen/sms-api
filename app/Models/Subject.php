<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Subject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function school(): BelongsTo      { return $this->belongsTo(School::class); }
    public function assessments(): HasMany    { return $this->hasMany(Assessment::class); }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_subject_teacher'
        )->withPivot(['teacher_id', 'academic_year'])
            ->withTimestamps();
    }
}
