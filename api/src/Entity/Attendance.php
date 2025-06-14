<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\AttendanceEnum;
use App\Repository\AttendanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['attendance:read']],
    denormalizationContext: ['groups' => ['attendance:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'student' => 'exact',
    'classGroup' => 'exact',
    'date' => 'exact'
])]
class Attendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attendance:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attendanceRecords')]
    #[Groups(['attendance:read', 'attendance:write'])]
    #[Assert\NotNull]
    private ?Student $student = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['attendance:read', 'attendance:write'])]
    #[Assert\NotNull]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(enumType: AttendanceEnum::class)]
    #[Groups(['attendance:read', 'attendance:write'])]
    #[Assert\NotNull]
    private ?AttendanceEnum $status = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attendance:read', 'attendance:write'])]
    private ?ClassGroup $classGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?AttendanceEnum
    {
        return $this->status;
    }

    public function setStatus(AttendanceEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getClassGroup(): ?ClassGroup
    {
        return $this->classGroup;
    }

    public function setClassGroup(?ClassGroup $classGroup): static
    {
        $this->classGroup = $classGroup;

        return $this;
    }
}
