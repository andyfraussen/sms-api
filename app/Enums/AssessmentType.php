<?php
namespace App\Enums;

enum AssessmentType: string
{
    case QUIZ = 'quiz';
    case TEST = 'test';
    case EXAM = 'exam';
    case ASSIGNMENT = 'assignment';
}
