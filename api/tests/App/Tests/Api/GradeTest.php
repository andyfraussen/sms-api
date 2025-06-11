<?php

namespace App\Tests\Api;

use App\Entity\School;
use App\Factory\GradeFactory;
use App\Factory\SchoolFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GradeTest extends CustomApiTest
{
    use ResetDatabase;
    use Factories;

    public function testCreateGrade(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);
        $this->makeRequest('POST', '/grades', [
            'name' => 'Grade 1',
            'school' => $schoolIri
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'Grade 1',
        ]);
    }

    public function testGetGradeCollection(): void
    {
        $school = SchoolFactory::createOne();
        GradeFactory::createOne(['name' => 'Grade A', 'school' => $school]);

        $response = $this->makeRequest('GET', '/grades');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertGreaterThan(0, count($data['hydra:member'] ?? []));
    }

    public function testCreateGradeValidationError(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $this->makeRequest('POST', '/grades', [
            'name' => '',
            'school' => $schoolIri,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testGetSingleGrade(): void
    {
        $grade = GradeFactory::createOne();
        $this->makeRequest('GET', '/grades/' . $grade->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => $grade->getName()]);
    }

    public function testPatchGrade(): void
    {
        $grade = GradeFactory::createOne();

        $this->makeRequest('PATCH', '/grades/' . $grade->getId(), [
            'name' => 'New Name',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'New Name']);
    }

    public function testDeleteGrade(): void
    {
        $grade = GradeFactory::createOne();

        $this->makeRequest('DELETE', '/grades/' . $grade->getId());
        $this->assertResponseStatusCodeSame(204);

        $this->makeRequest('GET', '/grades/' . $grade->getId());
        $this->assertResponseStatusCodeSame(301);
    }
}
