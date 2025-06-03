<?php
namespace App\Enums;

enum AssessmentType: string
{
    case Quiz = 'quiz';
    case Test = 'test';
    case Exam = 'exam';
    case Assignment = 'assignment';
}
