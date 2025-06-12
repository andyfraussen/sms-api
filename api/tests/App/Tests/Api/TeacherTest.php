<?php

namespace App\Tests\Api;

use App\Entity\ClassGroup;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use App\Factory\ClassGroupFactory;
use App\Factory\GradeFactory;
use App\Factory\SchoolFactory;
use App\Factory\SubjectFactory;
use App\Factory\UserFactory;
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
        $schoolIri = $this->findIriBy(\App\Entity\School::class, ['id' => $school->getId()]);

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
        $classGroupIri = $this->findIriBy(\App\Entity\ClassGroup::class, ['id' => $classGroup->getId()]);

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

}