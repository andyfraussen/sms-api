<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => AssessmentType::class,
            'date' => 'date',
        ];
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }

    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
