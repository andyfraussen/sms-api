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

    public function testCreateGradeWithoutSchool(): void
    {
        $this->makeRequest('POST', '/grades', [
            'name' => 'No School Grade',
        ]);

        $this->assertResponseStatusCodeSame((500));
    }

    public function testDeleteGradeWithClassGroups(): void
    {
        $grade = GradeFactory::createOne();
        \App\Factory\ClassGroupFactory::createOne(['grade' => $grade]);

        $this->makeRequest('DELETE', '/grades/' . $grade->getId());

        // Expect either a 204 (if cascade) or 500/409 (if DB constraint fails)
        $this->assertResponseStatusCodeSame(204);
    }

    public function testGetGradesFilteredBySchool(): void
    {
        $schoolA = SchoolFactory::createOne();
        $schoolB = SchoolFactory::createOne();

        GradeFactory::createOne(['name' => 'Grade A1', 'school' => $schoolA]);
        GradeFactory::createOne(['name' => 'Grade B1', 'school' => $schoolB]);

        $response = $this->makeRequest('GET', '/grades?school=' . $schoolA->getId());
        $data = $response->toArray();

        foreach ($data['hydra:member'] as $grade) {
            $this->assertStringContainsString('Grade A', $grade['name']);
        }
    }

    public function testPatchGradeSchool(): void
    {
        $grade = GradeFactory::createOne();
        $newSchool = SchoolFactory::createOne();

        $this->makeRequest('PATCH', '/grades/' . $grade->getId(), [
            'school' => $this->findIriBy(School::class, ['id' => $newSchool->getId()])
        ]);

        $this->assertResponseIsSuccessful();
    }
}
