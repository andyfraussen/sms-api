<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $guarded = [];

    /* Relationships ------------------------------------------------------ */
    public function grades(): HasMany   { return $this->hasMany(Grade::class); }
    public function subjects(): HasMany { return $this->hasMany(Subject::class); }
}
