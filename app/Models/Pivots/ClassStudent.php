<?php
namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassStudent extends Pivot
{
    use SoftDeletes;

    protected $table   = 'class_student';
    public    $incrementing = true;

    protected function casts(): array
    {
        return [
            'enrolled_from' => 'date',
            'enrolled_to'   => 'date',
        ];
    }
}
