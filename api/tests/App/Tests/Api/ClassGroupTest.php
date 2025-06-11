<?php
namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ClassGroup;
use App\Entity\Grade;
use App\Entity\School;
use App\Factory\ClassGroupFactory;
use App\Factory\SchoolFactory;
use App\Factory\GradeFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ClassGroupTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testCreateClassGroup(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);

        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);
        $gradeIri = $this->findIriBy(Grade::class, ['name' => $grade->getName()]);

        $client->request('POST', '/class_groups', [
            'json' => [
                'name' => 'Class Alpha',
                'school' => $schoolIri,
                'grade' => $gradeIri,
            ],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['name' => 'Class Alpha']);
    }

    public function testGetClassGroupCollection(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);
        ClassGroupFactory::createOne([
            'name' => 'Class Beta',
            'grade' => $grade,
            'school' => $school
        ]);

        $client->request('GET', '/class_groups', [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $groups = $client->getResponse()->toArray();
        $this->assertGreaterThan(0, count($groups['hydra:member'] ?? []));
    }

    public function testCreateClassGroupValidationError(): void
    {
        $client = self::createClient();
        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);

        $schoolIri = $this->findIriBy(School::class, ['name' => $school->getName()]);
        $gradeIri = $this->findIriBy(Grade::class, ['name' => $grade->getName()]);

        $client->request('POST', '/class_groups', [
            'json' => [
                'name' => '',
                'school' => $schoolIri,
                'grade' => $gradeIri
            ],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testPatchClassGroup(): void
    {
        $client = self::createClient();

        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);
        $classGroup = ClassGroupFactory::createOne([
            'name' => 'Old Name',
            'grade' => $grade,
            'school' => $school
        ]);

        $iri = $this->findIriBy(ClassGroup::class, ['name' => 'Old Name']);

        $client->request('PATCH', $iri, [
            'json' => ['name' => 'New Name'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'New Name']);
    }

    public function testDeleteClassGroup(): void
    {
        $client = self::createClient();

        $school = SchoolFactory::createOne();
        $grade = GradeFactory::createOne(['school' => $school]);
        $classGroup = ClassGroupFactory::createOne([
            'name' => 'Delete Me',
            'grade' => $grade,
            'school' => $school
        ]);

        $iri = $this->findIriBy(ClassGroup::class, ['name' => 'Delete Me']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', $iri, [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidGradeOrSchool(): void
    {
        $client = self::createClient();

        $client->request('POST', '/class_groups', [
            'json' => [
                'name' => 'Invalid Links',
                'school' => '/schools/999999',
                'grade' => '/grades/999999'
            ],
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }
}
