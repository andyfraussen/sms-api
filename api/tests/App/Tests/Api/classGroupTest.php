<?php

    namespace App\Tests\Api;

    use App\Entity\ClassGroup;
    use App\Entity\Grade;
    use App\Entity\School;
    use App\Entity\Teacher;
    use App\Factory\ClassGroupFactory;
    use App\Factory\SchoolFactory;
    use App\Factory\GradeFactory;
    use App\Factory\TeacherFactory;
    use Zenstruck\Foundry\Test\Factories;
    use Zenstruck\Foundry\Test\ResetDatabase;

    class classGroupTest extends CustomApiTest
    {
        use ResetDatabase;
        use Factories;

        public function testCreateClassGroup(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);

            $this->makeRequest('POST', '/class_groups', [
                'name' => 'Class Alpha',
                'school' => $this->findIriBy(School::class, ['name' => $school->getName()]),
                'grade' => $this->findIriBy(Grade::class, ['name' => $grade->getName()]),
            ]);

            $this->assertResponseStatusCodeSame(201);
            $this->assertJsonContains(['name' => 'Class Alpha']);
        }

        public function testGetClassGroupCollection(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);

            ClassGroupFactory::createOne([
                'name' => 'Class Beta',
                'school' => $school,
                'grade' => $grade,
            ]);

            $response = $this->makeRequest('GET', '/class_groups');
            $this->assertResponseIsSuccessful();

            $data = $response->toArray();
            $this->assertGreaterThan(0, count($data['hydra:member'] ?? []));
        }

        public function testCreateClassGroupValidationError(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);

            $this->makeRequest('POST', '/class_groups', [
                'name' => '',
                'school' => $this->findIriBy(School::class, ['name' => $school->getName()]),
                'grade' => $this->findIriBy(Grade::class, ['name' => $grade->getName()]),
            ]);

            $this->assertResponseStatusCodeSame(422);
            $this->assertJsonContains(['@type' => 'ConstraintViolation']);
        }

        public function testPatchClassGroup(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);

            ClassGroupFactory::createOne([
                'name' => 'Old Name',
                'school' => $school,
                'grade' => $grade,
            ]);

            $iri = $this->findIriBy(ClassGroup::class, ['name' => 'Old Name']);

            $this->makeRequest('PATCH', $iri, ['name' => 'New Name']);

            $this->assertResponseIsSuccessful();
            $this->assertJsonContains(['name' => 'New Name']);
        }

        public function testDeleteClassGroup(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);

            ClassGroupFactory::createOne([
                'name' => 'Delete Me',
                'school' => $school,
                'grade' => $grade,
            ]);

            $iri = $this->findIriBy(ClassGroup::class, ['name' => 'Delete Me']);

            $this->makeRequest('DELETE', $iri);
            $this->assertResponseStatusCodeSame(204);

            $this->makeRequest('GET', $iri);
            $this->assertResponseStatusCodeSame(404);
        }

        public function testInvalidGradeOrSchool(): void
        {
            $this->makeRequest('POST', '/class_groups', [
                'name' => 'Invalid Links',
                'school' => '/schools/99999',
                'grade' => '/grades/999999',
            ]);

            $this->assertResponseStatusCodeSame(400);
        }

        public function testCreateClassGroupWithoutGrade(): void
        {
            $school = SchoolFactory::createOne();

            $response = $this->makeRequest('POST', '/class_groups', [
                'name' => 'No Grade',
                'school' => $this->findIriBy(School::class, ['name' => $school->getName()]),
            ]);

            $this->assertResponseStatusCodeSame(500);
        }

        public function testAssignSubjectsToClassGroup(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);
            $subject = \App\Factory\SubjectFactory::createOne();
            $subjectIri = $this->findIriBy(\App\Entity\Subject::class, ['id' => $subject->getId()]);

            $response = $this->makeRequest('POST', '/class_groups', [
                'name' => 'With Subjects',
                'school' => $this->findIriBy(School::class, ['id' => $school->getId()]),
                'grade' => $this->findIriBy(Grade::class, ['id' => $grade->getId()]),
                'subjects' => [$subjectIri],
            ]);

            $this->assertResponseStatusCodeSame(201);
            $data = $response->toArray();
            $this->assertArrayHasKey('subjects', $data);
            $this->assertContains($subjectIri, $data['subjects']);
        }

        public function testRemoveSubjectsFromClassGroup(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);
            $subject = \App\Factory\SubjectFactory::createOne();

            $classGroup = ClassGroupFactory::createOne([
                'name' => 'With Subjects',
                'school' => $school,
                'grade' => $grade,
                'subjects' => [$subject],
            ]);

            $iri = $this->findIriBy(ClassGroup::class, ['name' => 'With Subjects']);

            $response = $this->makeRequest('PATCH', $iri, [
                'subjects' => [],
            ]);

            $this->assertResponseIsSuccessful();
            $data = $response->toArray();
            $this->assertArrayHasKey('subjects', $data);
            $this->assertEmpty($data['subjects']);
        }
        public function testDeleteClassGroupWithTeachers(): void
        {
            $school = SchoolFactory::createOne();
            $grade = GradeFactory::createOne(['school' => $school]);
            $teacher = TeacherFactory::createOne();

            $classGroup = ClassGroupFactory::createOne([
                'school' => $school,
                'grade' => $grade,
                'teachers' => [$teacher],
            ]);

            $iri = $this->findIriBy(ClassGroup::class, ['id' => $classGroup->getId()]);

            $this->makeRequest('DELETE', $iri);
            $this->assertResponseStatusCodeSame(204);
        }
    }
