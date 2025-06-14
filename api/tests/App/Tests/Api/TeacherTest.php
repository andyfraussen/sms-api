<?php

namespace App\Tests\Api;

use App\Entity\ClassGroup;
use App\Entity\School;
use App\Factory\ClassGroupFactory;
use App\Factory\SchoolFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TeacherTest extends CustomApiTest
{
    use ResetDatabase;
    use Factories;

    public function testCreateTeacher(): void
    {
        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'teacher@email.com',
                'password' => 'password',
            ],
        ]);

        $data = $response->toArray();

        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals('teacher@email.com', $data['user']['email']);
    }

    public function testGetTeacherCollection(): void
    {
        $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'teacher1@email.com',
                'password' => 'password',
            ],
        ]);

        $response = $this->makeRequest('GET', '/teachers');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertGreaterThan(0, count($data['hydra:member'] ?? []));
    }

    public function testPatchTeacherEmail(): void
    {
        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'patchme@email.com',
                'password' => 'password',
            ],
        ]);

        $iri = $response->toArray()['@id'];

        $this->makeRequest('PATCH', $iri, [
            'user' => [
                'email' => 'updated@email.com',
            ],
        ]);

        $updated = $this->makeRequest('GET', $iri)->toArray();
        $this->assertEquals('updated@email.com', $updated['user']['email']);
    }

    public function testDeleteTeacher(): void
    {
        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'delete@email.com',
                'password' => 'password',
            ],
        ]);

        $iri = $response->toArray()['@id'];

        $this->makeRequest('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);

        $this->makeRequest('GET', $iri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testAssignTeacherToSchool(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['id' => $school->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'relate@email.com',
                'password' => 'password',
            ],
            'schools' => [$schoolIri],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = $response->toArray();
        $this->assertArrayHasKey('schools', $data);
        $this->assertContains($schoolIri, $data['schools']);
    }

    public function testAssignTeacherToClassGroup(): void
    {
        $classGroup = ClassGroupFactory::createOne();
        $classGroupIri = $this->findIriBy(ClassGroup::class, ['id' => $classGroup->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'relate@email.com',
                'password' => 'password',
            ],
            'classGroups' => [$classGroupIri],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = $response->toArray();
        $this->assertArrayHasKey('classGroups', $data);
        $this->assertContains($classGroupIri, $data['classGroups']);
    }

    public function testAssignMultipleTeacherToClassGroup(): void
    {
        $classGroup = ClassGroupFactory::createOne();
        $classGroupIri = $this->findIriBy(ClassGroup::class, ['id' => $classGroup->getId()]);


        $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'teacher1@email.com',
                'password' => 'password',
            ],
            'classGroups' => [$classGroupIri],
        ]);
        $this->assertResponseStatusCodeSame(201);


        $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'teache2@email.com',
                'password' => 'password',
            ],
            'classGroups' => [$classGroupIri],
        ]);
        $this->assertResponseStatusCodeSame(201);


        $response = $this->makeRequest('GET', '/teachers?user.email[]=teacher1@email.com&user.email[]=teache2@email.com');
        $this->assertResponseStatusCodeSame(200);

        $data = $response->toArray();

        foreach ($data['hydra:member'] ?? [] as $teacher) {
            $this->assertArrayHasKey('classGroups', $teacher, 'Teacher does not have classGroups key');
            $this->assertContains($classGroupIri, $teacher['classGroups'], 'classGroup IRI not found in teacher classGroups');
        }
    }

        public function testRemoveTeacherFromClassGroup(): void
    {
        $classGroup = ClassGroupFactory::createOne();
        $classGroupIri = $this->findIriBy(ClassGroup::class, ['id' => $classGroup->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'relate@email.com',
                'password' => 'password',
            ],
            'classGroups' => [$classGroupIri],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = $response->toArray();

        $this->assertArrayHasKey('classGroups', $data);
        $this->assertContains($classGroupIri, $data['classGroups']);

        $teacherIri = $data['@id'];
        $response =    $this->makeRequest('PATCH', $teacherIri, [
            'classGroups' => [
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $this->assertArrayHasKey('classGroups', $data);
        $this->assertEmpty($data['classGroups']);
    }

    public function testAssignTeacherToMultipleSchools(): void
    {
        $school1 = SchoolFactory::createOne();
        $school2 = SchoolFactory::createOne();
        $schoolIri1 = $this->findIriBy(School::class, ['id' => $school1->getId()]);
        $schoolIri2 = $this->findIriBy(School::class, ['id' => $school2->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'multischool@email.com',
                'password' => 'password',
            ],
            'schools' => [$schoolIri1, $schoolIri2],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertCount(2, $data['schools']);
    }

    public function testRemoveTeacherFromSchool(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['id' => $school->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'removeschool@email.com',
                'password' => 'password',
            ],
            'schools' => [$schoolIri],
        ]);

        $teacherIri = $response->toArray()['@id'];

        $patchResponse = $this->makeRequest('PATCH', $teacherIri, [
            'schools' => [],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $patchResponse->toArray();
        $this->assertEmpty($data['schools']);
    }

    public function testAssignTeacherToSubject(): void
    {
        $subject = \App\Factory\SubjectFactory::createOne();
        $subjectIri = $this->findIriBy(\App\Entity\Subject::class, ['id' => $subject->getId()]);

        $response = $this->makeRequest('POST', '/teachers', [
            'user' => [
                'email' => 'subject@email.com',
                'password' => 'password',
            ],
            'subjects' => [$subjectIri],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertArrayHasKey('subjects', $data);
        $this->assertContains($subjectIri, $data['subjects']);
    }

}