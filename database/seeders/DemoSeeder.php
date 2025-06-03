<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    School, Grade, SchoolClass, Subject,
    User, Student, Attendance, Assessment
};
use App\Models\Pivots\{ClassStudent, ClassSubjectTeacher};
use Spatie\Permission\Models\Role;
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            ['name' => 'admin',   'guard_name' => 'web'],
            ['name' => 'teacher', 'guard_name' => 'web'],
            ['name' => 'parent',  'guard_name' => 'web'],
        ], ['name']);

        /* ─────────────────────────────
         *  1.  Core reference data
         * ───────────────────────────── */
        $school  = School::factory()->create(['name' => 'Demo International School']);

        $grades  = Grade::factory(3)->sequence(
            ['name' => 'Grade 1'],
            ['name' => 'Grade 2'],
            ['name' => 'Grade 3'],
        )->create(['school_id' => $school->id]);

        $subjects = Subject::factory()->count(4)->sequence(
            ['name' => 'Mathematics'],
            ['name' => 'Science'],
            ['name' => 'English'],
            ['name' => 'History'],
        )->create(['school_id' => $school->id]);

        /* ─────────────────────────────
         *  2.  Users (roles)
         * ───────────────────────────── */
        $admin   = User::factory()->create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');

        $teachers = User::factory(3)->sequence(
            ['name' => 'Alice Teacher',  'email' => 'alice.teacher@example.com'],
            ['name' => 'Bob Teacher',    'email' => 'bob.teacher@example.com'],
            ['name' => 'Carol Teacher',  'email' => 'carol.teacher@example.com'],
        )->create(['password' => Hash::make('password')]);

        $teachers->each->assignRole('teacher');

        $parents  = User::factory(2)->sequence(
            ['name' => 'Peter Parent',  'email' => 'peter.parent@example.com'],
            ['name' => 'Paula Parent',  'email' => 'paula.parent@example.com'],
        )->create(['password' => Hash::make('password')]);

        $parents->each->assignRole('parent');

        /* ─────────────────────────────
         *  3.  Classes & teacher load
         * ───────────────────────────── */
        $classes = $grades->flatMap(function ($grade) {
            return SchoolClass::factory(2)->create(['grade_id' => $grade->id]);
        });

        // attach each subject to each class with a teacher for the 2024-2025 year
        foreach ($classes as $class) {
            foreach ($subjects as $i => $subject) {
                $class->subjectTeachers()->attach(
                    $subject->id,
                    [
                        'teacher_id'   => $teachers[$i % $teachers->count()]->id,
                        'academic_year'=> '2024-25',
                    ]
                );
            }
        }

        /* ─────────────────────────────
         *  4.  Students & parent links
         * ───────────────────────────── */
        $students = Student::factory(10)->make()->each(function ($student) use ($classes) {
            $currentClass = $classes->random();
            $student->school_class_id = $currentClass->id;
            $student->save();

            // create historic enrolment row
            $student->classes()->attach(
                $currentClass->id,
                [
                    'enrolled_from' => now()->subMonths(rand(1, 9)),
                    'enrolled_to'   => null,
                ]
            );
        });

        // link first two students to the demo parents
        $parents[0]->children()->attach($students[0]->id, ['relationship' => 'father']);
        $parents[1]->children()->attach($students[1]->id, ['relationship' => 'mother']);

        /* ─────────────────────────────
         *  5.  Operational data
         * ───────────────────────────── */
        // Attendance: mark “today” for every student
        foreach ($students as $student) {
            Attendance::factory()->create([
                'student_id'  => $student->id,
                'date'        => today(),
                'status'      => 'present',
                'recorded_by' => $teachers->random()->id,
            ]);
        }

        // A quick assessment in Math for each student
        foreach ($students as $student) {
            Assessment::factory()->create([
                'student_id'  => $student->id,
                'subject_id'  => $subjects->firstWhere('name', 'Mathematics')->id,
                'name'        => 'Chapter-1 Quiz',
                'type'        => 'quiz',
                'score'       => rand(7, 10),
                'max_score'   => 10,
                'graded_by'   => $teachers->first()->id,
                'date'        => today(),
            ]);
        }
    }
}
