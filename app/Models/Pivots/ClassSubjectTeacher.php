<?php
namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassSubjectTeacher extends Pivot
{
    protected $table = 'class_subject_teacher';

    protected function casts(): array
    {
        return ['academic_year' => 'string'];
    }
}
