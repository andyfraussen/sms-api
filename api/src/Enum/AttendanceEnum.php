<?php
namespace App\Enum;
enum AttendanceEnum: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case AUTHORIZED_ABSENCE = 'authorized absence';
}