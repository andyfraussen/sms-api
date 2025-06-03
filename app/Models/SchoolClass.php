<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, HasMany, BelongsToMany
};
use App\Models\Pivots\{ClassSubjectTeacher, ClassStudent};

class SchoolClass extends Model
{
    use HasFactory;

    protected $guarded = [];

    /* Relationships */
    public function grade(): BelongsTo { return $this->belongsTo(Grade::class); }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** Historical enrolments (class_student) */
    public function enrolments(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'class_student'
        )->using(ClassStudent::class)
            ->withPivot(['enrolled_from', 'enrolled_to'])
            ->withTimestamps();
    }

    /** Subjects taught in this class with the teacher for a specific year */
    public function subjectTeachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_teacher'
        )->using(ClassSubjectTeacher::class)
            ->withPivot(['teacher_id', 'academic_year'])
            ->withTimestamps();
    }
}
