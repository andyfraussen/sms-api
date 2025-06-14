<?php

namespace App\Tests\Api;

use App\Entity\Attendance;
use App\Entity\ClassGroup;
use App\Entity\Student;
use App\Enum\AttendanceEnum;
use App\Factory\AttendanceFactory;
use App\Factory\ClassGroupFactory;
use App\Factory\StudentFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AttendanceTest extends CustomApiTest
{
    use ResetDatabase;
    use Factories;

    public function testCreateAttendance(): void
    {
        $student = StudentFactory::createOne();
        $classGroup = ClassGroupFactory::createOne();

        $response = $this->makeRequest('POST', '/attendances', [
            'student' => $this->findIriBy(Student::class, ['id' => $student->getId()]),
            'classGroup' => $this->findIriBy(ClassGroup::class, ['id' => $classGroup->getId()]),
            'date' => '2025-06-14',
            'status' => 'present'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'status' => 'present',
        ]);
    }

    public function testGetAttendanceCollection(): void
    {
        AttendanceFactory::createMany(3);

        $response = $this->makeRequest('GET', '/attendances');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertGreaterThanOrEqual(3, count($data['hydra:member'] ?? []));
    }

    public function testCreateAttendanceValidationError(): void
    {
        $this->makeRequest('POST', '/attendances', [
            'status' => 'present',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testPatchAttendanceStatus(): void
    {
        $attendance = AttendanceFactory::createOne(['status' => AttendanceEnum::PRESENT]);
        $iri = $this->findIriBy(Attendance::class, ['id' => $attendance->getId()]);

        $this->makeRequest('PATCH', $iri, ['status' => 'absent']);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 'absent']);
    }

    public function testDeleteAttendance(): void
    {
        $attendance = AttendanceFactory::createOne();
        $iri = $this->findIriBy(Attendance::class, ['id' => $attendance->getId()]);

        $this->makeRequest('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);

        $this->makeRequest('GET', $iri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testFilterAttendanceByStudent(): void
    {
        $studentA = StudentFactory::createOne();
        $studentB = StudentFactory::createOne();

        AttendanceFactory::createOne(['student' => $studentA]);
        AttendanceFactory::createOne(['student' => $studentB]);

        $studentIri = $this->findIriBy(\App\Entity\Student::class, ['id' => $studentA->getId()]);

        $response = $this->makeRequest('GET', '/attendances?student=' . urlencode($studentIri));
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        foreach ($data['hydra:member'] as $item) {
            $this->assertEquals($studentIri, $item['student']);
        }
    }

    public function testFilterAttendanceByClassGroup(): void
    {
        $classGroupA = ClassGroupFactory::createOne();
        $classGroupB = ClassGroupFactory::createOne();

        AttendanceFactory::createOne(['classGroup' => $classGroupA]);
        AttendanceFactory::createOne(['classGroup' => $classGroupB]);

        $classGroupIri = $this->findIriBy(\App\Entity\ClassGroup::class, ['id' => $classGroupA->getId()]);

        $response = $this->makeRequest('GET', '/attendances?classGroup=' . urlencode($classGroupIri));
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        foreach ($data['hydra:member'] as $item) {
            $this->assertEquals($classGroupIri, $item['classGroup']);
        }
    }

    public function testFilterAttendanceByDate(): void
    {
        $date = new \DateTime('2025-06-14');

        AttendanceFactory::createOne(['date' => $date]);
        AttendanceFactory::createOne(['date' => new \DateTime('2025-06-13')]);

        $response = $this->makeRequest('GET', '/attendances?date=' . $date->format('Y-m-d'));
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();

        foreach ($data['hydra:member'] as $item) {
            $actualDate = (new \DateTime($item['date']))->format('Y-m-d');
            $this->assertEquals($date->format('Y-m-d'), $actualDate);
        }
    }
}
