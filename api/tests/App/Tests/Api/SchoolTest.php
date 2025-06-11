<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\School;
use App\Factory\GradeFactory;
use App\Factory\SchoolFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SchoolTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    public function testCreateSchool(): void
    {
        $client = self::createClient();
        $client->request('POST', '/schools', [
            'json' => [
                'name' => 'Riverdale High',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@type' => 'School',
            'name' => 'Riverdale High',
        ]);
    }

    public function testDuplicateSchoolNameValidation(): void
    {
        $client = self::createClient();
        SchoolFactory::createOne(['name' => 'Unique School']);

        $client->request('POST', '/schools', [
            'json' => ['name' => 'Unique School'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testGetSchoolCollection(): void
    {
        $client = self::createClient();

        $client->request('POST', '/schools', [
            'json' => ['name' => 'School One'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $client->request('POST', '/schools', [
            'json' => ['name' => 'School Two'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $client->request('GET', '/schools', [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->toArray();

        $names = array_map(fn($item) => $item['name'], $content['hydra:member'] ?? []);

        $this->assertContains('School One', $names);
        $this->assertContains('School Two', $names);
    }

    public function testGetSchoolItem(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/schools', [
            'json' => ['name' => 'Test School'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $iri = $response->toArray()['@id'];

        $client->request('GET', $iri, [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@id' => $iri,
            'name' => 'Test School',
        ]);
    }

    public function testUpdateSchool(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/schools', [
            'json' => ['name' => 'Old Name'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $iri = $response->toArray()['@id'];

        $client->request('PATCH', $iri, [
            'json' => ['name' => 'New Name'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/School',
            '@id' => $iri,
            'name' => 'New Name',
        ]);
    }

    public function testDeleteSchool(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/schools', [
            'json' => ['name' => 'To Be Deleted'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $iri = $response->toArray()['@id'];

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', $iri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateSchoolValidationError(): void
    {
        self::createClient()->request('POST', '/schools', [
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
        $client = self::createClient();

        $client->request('POST', '/schools', [
            'json' => ['name' => 'School One'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $client->request('POST', '/schools', [
            'json' => ['name' => 'School Two'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $client->request('GET', '/schools?name=One', ['headers' => ['Accept' => 'application/ld+json']]);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $content = $response->toArray();

        $names = array_map(fn($item) => $item['name'], $content['hydra:member']);
        $this->assertContains('School One', $names);
        $this->assertEquals(1, $content['hydra:totalItems']);

    }

    public function testCreateDuplicateSchoolNameFails(): void
    {
        $client = self::createClient();

        $client->request('POST', '/schools', [
            'json' => ['name' => 'Unique Name'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/schools', [
            'json' => ['name' => 'Unique Name'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testPatchSchoolValidationError(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/schools', [
            'json' => ['name' => 'Valid Name'],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $iri = $response->toArray()['@id'];

        $client->request('PATCH', $iri, [
            'json' => ['name' => ''],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testSchoolPagination(): void
    {
        $client = self::createClient();

        for ($i = 1; $i <= 10; $i++) {
            $client->request('POST', '/schools', [
                'json' => ['name' => "School $i"],
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]);
        }

        $client->request('GET', '/schools?page=1&itemsPerPage=2', ['headers' => ['Accept' => 'application/ld+json']]);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse()->toArray();

        $this->assertArrayHasKey('hydra:member', $response);
        $this->assertArrayHasKey('hydra:totalItems', $response);

        $this->assertLessThanOrEqual(2, count($response['hydra:member']));
        $this->assertEquals(10, $response['hydra:totalItems']);
        $this->assertEquals("/schools?itemsPerPage=2&page=5", $response['hydra:view']['hydra:last']);
    }

    public function testSchoolsAreSortedByNameAsc(): void
    {
        $client = self::createClient();

        $client->request('POST', '/schools', ['json' => ['name' => 'Zeta'], 'headers' => ['Content-Type' => 'application/ld+json']]);
        $client->request('POST', '/schools', ['json' => ['name' => 'Alpha'], 'headers' => ['Content-Type' => 'application/ld+json']]);

        $response = $client->request('GET', '/schools');
        $this->assertResponseIsSuccessful();

        $content = $response->toArray();
        $names = array_column($content['hydra:member'], 'name');
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
        $client = self::createClient();

        for ($i = 1; $i <= 35; $i++) {
            $client->request('POST', '/schools', [
                'json' => ['name' => "School $i"],
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]);
        }

        $response = $client->request('GET', '/schools?page=1&itemsPerPage=10');
        $data = $response->toArray();

        $this->assertCount(10, $data['hydra:member']);
        $this->assertEquals(35, $data['hydra:totalItems']);
    }

    public function testUpdateNonExistentSchool(): void
    {
        $client = self::createClient();

        $client->request('PATCH', '/schools/99999', [
            'json' => ['name' => 'Should Fail'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSchoolIncludesGradesInSerialization(): void
    {
        $client = self::createClient();

        $school = SchoolFactory::createOne();
        GradeFactory::createMany(2, ['school' => $school]);

        $iri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $client->request('GET', $iri, [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray();

        $this->assertArrayHasKey('grades', $data);
        $this->assertCount(2, $data['grades']);
    }
}