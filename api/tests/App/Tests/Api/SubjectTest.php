<?php

namespace App\Tests\Api;

use App\Entity\ClassGroup;
use App\Entity\Subject;
use App\Factory\ClassGroupFactory;
use App\Factory\SubjectFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SubjectTest extends CustomApiTest
{
    use ResetDatabase;
    use Factories;

    public function testAddSubjectToClassGroup(): void
    {
        $subject = SubjectFactory::createOne();
        $classGroup = ClassGroupFactory::createOne();

        $classGroupIri = $this->findIriBy(ClassGroup::class, ['name' => $classGroup->getName()]);
        $subjectIri = $this->findIriBy(Subject::class, ['name' => $subject->getName()]);

        $response = $this->makeRequest('PATCH', $classGroupIri, [
            'subjects' => [$subjectIri],
        ]);
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'subjects' => [$subjectIri],
        ]);
    }

    public function testSubjectSerializationIncludesClassGroups(): void
    {
        $classGroup = ClassGroupFactory::createOne();
        SubjectFactory::createOne(['name' => 'Science']);

        $classGroupIri = $this->findIriBy(ClassGroup::class, ['name' => $classGroup->getName()]);;
        $subjectIri = $this->findIriBy(Subject::class, ['name' => 'Science']);

        $this->makeRequest('PATCH', $subjectIri, ['classGroups' => [$classGroupIri]]);

        $response = $this->makeRequest('GET', $subjectIri);
        $data = $response->toArray();

        $this->assertArrayHasKey('classGroups', $data);
        $this->assertContains($classGroupIri, $data['classGroups']);
    }

    public function testClassGroupSerializationIncludesSubjects(): void
    {
        $classGroup = ClassGroupFactory::createOne();
        SubjectFactory::createOne(['name' => 'Science']);

        $classGroupIri = $this->findIriBy(ClassGroup::class, ['name' => $classGroup->getName()]);;
        $subjectIri = $this->findIriBy(Subject::class, ['name' => 'Science']);

        $this->makeRequest('PATCH', $subjectIri, ['classGroups' => [$classGroupIri]]);

        $response = $this->makeRequest('GET', $classGroupIri);
        $data = $response->toArray();

        $this->assertArrayHasKey('subjects', $data);
        $this->assertContains($subjectIri, $data['subjects']);
    }
}
