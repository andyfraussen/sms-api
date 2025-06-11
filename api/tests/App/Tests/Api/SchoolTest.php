<?php

namespace App\Tests\Api;

use App\Entity\Grade;
use App\Entity\School;
use App\Factory\GradeFactory;
use App\Factory\SchoolFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SchoolTest extends CustomApiTest
{
    use ResetDatabase;
    use Factories;
    public function testCreateSchool(): void
    {
        $this->makeRequest(
            'POST',
            '/schools',
            ['name' => 'Riverdale High'],
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@type' => 'School',
            'name' => 'Riverdale High',
        ]);
    }

    public function testDuplicateSchoolNameValidation(): void
    {
        SchoolFactory::createOne(['name' => 'Unique School']);

        $this->makeRequest(
            'POST',
            '/schools',
            ['name' => 'Unique School'],
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testGetSchoolCollection(): void
    {
        SchoolFactory::createOne(['name' => 'School One']);
        SchoolFactory::createOne(['name' => 'School Two']);

        $response = $this->makeRequest('GET', '/schools');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();

        $names = array_map(fn($item) => $item['name'], $data['hydra:member'] ?? []);

        $this->assertContains('School One', $names);
        $this->assertContains('School Two', $names);
    }

    public function testGetSchoolItem(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);;

        $this->makeRequest('GET', $schoolIri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@id' => $schoolIri,
            'name' => $school->getName(),
        ]);
    }

    public function testUpdateSchool(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);;

        $this->makeRequest('PATCH', $schoolIri, ['name' => 'New Name']);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@id' => $schoolIri,
            'name' => 'New Name',
        ]);
    }

    public function testDeleteSchool(): void
    {
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);;

        $this->makeRequest('DELETE', $schoolIri);

        $this->assertResponseStatusCodeSame(204);

        $this->makeRequest('GET', $schoolIri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateSchoolValidationError(): void
    {
        $this->makeRequest('POST', '/schools', [
            'json' => [
                'name' => '',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@type' => 'ConstraintViolation',
        ]);
    }

    public function testFilterSchoolsByName(): void
    {
        $schoolOneName = SchoolFactory::createOne()->getName();
        SchoolFactory::createOne();

        $response = $this->makeRequest('GET', '/schools?name=' . $schoolOneName);;

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $names = array_map(fn($item) => $item['name'], $data['hydra:member']);

        $this->assertContains($schoolOneName, $names);
        $this->assertEquals(1, $data['hydra:totalItems']);

    }

    public function testCreateDuplicateSchoolNameFails(): void
    {
        $schoolOneName = SchoolFactory::createOne()->getName();

        $this->makeRequest('POST', '/schools', ['name' => $schoolOneName]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testPatchSchoolValidationError(): void
    {
        $school = SchoolFactory::createOne();
        $iri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $this->makeRequest('PATCH', $iri, ['name' => '']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testSchoolPagination(): void
    {
        SchoolFactory::createMany(10);

        $response = $this->makeRequest('GET', '/schools?page=1&itemsPerPage=2');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertLessThanOrEqual(2, count($data['hydra:member']));
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertEquals("/schools?itemsPerPage=2&page=5", $data['hydra:view']['hydra:last']);
    }

    public function testSchoolsAreSortedByNameAsc(): void
    {
        SchoolFactory::createOne(['name' => 'Alpha']);
        SchoolFactory::createOne(['name' => 'Zeta']);

        $response = $this->makeRequest('GET', '/schools');
        $data = $response->toArray();

        $names = array_column($data['hydra:member'], 'name');
        $this->assertEquals(['Alpha', 'Zeta'], $names);
    }

    public function testInvalidJson(): void
    {
        $client = self::createClient();

        $client->request('POST', '/schools', [
            'body' => '{"name": "Test",}',
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testPaginationLimits(): void
    {
        SchoolFactory::createMany(35);

        $response = $this->makeRequest('GET', '/schools?page=1&itemsPerPage=10');
        $data = $response->toArray();

        $this->assertCount(10, $data['hydra:member']);
        $this->assertEquals(35, $data['hydra:totalItems']);
    }

    public function testUpdateNonExistentSchool(): void
    {
        $this->makeRequest('PATCH', '/schools/99999', ['name' => 'Should Fail']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSchoolIncludesGradesInSerialization(): void
    {
        $school = SchoolFactory::createOne();
        GradeFactory::createMany(2, ['school' => $school]);

        $iri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $response = $this->makeRequest('GET', $iri);
        $data = $response->toArray();

        $this->assertArrayHasKey('grades', $data);
        $this->assertCount(2, $data['grades']);
    }

    public function testDeletingSchoolAlsoDeletesGrades(): void
    {
        $school = SchoolFactory::createOne();

        $grade = GradeFactory::createOne(['school' => $school]);

        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);
        $gradeIri = $this->findIriBy(Grade::class, ['name' => $grade->getName()]);

        $this->makeRequest('DELETE', $schoolIri);
        $this->assertResponseStatusCodeSame(204);

        $this->makeRequest('GET', $gradeIri);
        $this->assertResponseStatusCodeSame(404);
    }

}