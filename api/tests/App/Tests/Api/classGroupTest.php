<?php

namespace App\Tests\Api;

use App\Entity\ClassGroup;
use App\Entity\Grade;
use App\Entity\School;
use App\Entity\Subject;
use App\Factory\ClassGroupFactory;
use App\Factory\SchoolFactory;
use App\Factory\GradeFactory;
use App\Factory\SubjectFactory;
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

    public function testCreateWithSubjects(): void
    {
        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);
        $subject = SubjectFactory::createOne();
        $classGroup = ClassGroupFactory::createOne([
            'name' => 'Class Alpha',
            'school' => $school,
            'grade' => $grade,
        ]);

        $classGroupIri = $this->findIriBy(ClassGroup::class, ['name' => $classGroup->getName()]);
        $subjectIri = $this->findIriBy(Subject::class, ['name' => $subject->getName()]);

        $this->makeRequest('PATCH', $classGroupIri, [
            'subjects' => [$subjectIri],
        ]);

        $data = $this->makeRequest('GET', $classGroupIri)->toArray();

        $this->assertArrayHasKey('subjects', $data);
        $this->assertContains($subjectIri, $data['subjects']);
    }

    public function testDeleteSubjects(): void
    {
        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);
        $subject = SubjectFactory::createOne();
        $classGroup = ClassGroupFactory::createOne([
            'name' => 'Class Alpha',
            'school' => $school,
            'grade' => $grade,
        ]);

        $classGroupIri = $this->findIriBy(ClassGroup::class, ['name' => $classGroup->getName()]);
        $subjectIri = $this->findIriBy(Subject::class, ['name' => $subject->getName()]);

        $this->makeRequest('PATCH', $classGroupIri, [
            'subjects' => [$subjectIri],
        ]);

        $data = $this->makeRequest('GET', $classGroupIri)->toArray();

        $this->assertArrayHasKey('subjects', $data);
        $this->assertContains($subjectIri, $data['subjects']);

        $data = $this->makeRequest('PATCH', $classGroupIri, [
            'subjects' => [],
        ])->toArray();

        $this->assertArrayHasKey('subjects', $data);
        $this->assertEmpty($data['subjects']);
    }
}
