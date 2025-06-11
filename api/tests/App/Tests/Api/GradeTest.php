<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\School;
use App\Factory\GradeFactory;
use App\Factory\SchoolFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GradeTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testCreateGrade(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);
        $client->request('POST', '/grades', [
            'json' => [
                'name' => 'Grade 1',
                'school' => $schoolIri
            ],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'Grade 1',
        ]);
    }

    public function testGetGradeCollection(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $client->request('POST', '/grades', [
            'json' => ['name' => 'Grade A', 'school' => $schoolIri],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $client->request('GET', '/grades', [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $grades = $client->getResponse()->toArray();

        $this->assertGreaterThan(0, count($grades['hydra:member'] ?? []));
    }

    public function testCreateGradeValidationError(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);

        $client->request('POST', '/grades', [
            'json' => ['name' => '', 'school' => $schoolIri],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testGetSingleGrade(): void
    {
        $client = self::createClient();
        $grade = GradeFactory::createOne();
        $client->request('GET', '/grades/' . $grade->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => $grade->getName()]);
    }

    public function testPatchGrade(): void
    {
        $client = self::createClient();
        $grade = GradeFactory::createOne(['name' => 'Old Name']);

        $client->request('PATCH', '/grades/' . $grade->getId(), [
            'json' => ['name' => 'New Name'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'New Name']);
    }

    public function testDeleteGrade(): void
    {
        $client = self::createClient();
        $grade = GradeFactory::createOne();

        $client->request('DELETE', '/grades/' . $grade->getId());
        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', '/grades/' . $grade->getId(), [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(301);
    }
}
