<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ApiResource]
class Student extends Person
{

    #[ORM\Column(length: 220)]
    private ?string $studentCode = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassGroup $currentClass = null;

    /**
     * @var Collection<int, Guardian>
     */
    #[ORM\ManyToMany(targetEntity: Guardian::class, inversedBy: 'students')]
    private Collection $guardians;

    /**
     * @var Collection<int, Attendance>
     */
    #[ORM\OneToMany(targetEntity: Attendance::class, mappedBy: 'student')]
    private Collection $attendanceRecords;

    public function __construct()
    {
        $this->guardians = new ArrayCollection();
        $this->attendanceRecords = new ArrayCollection();
    }

    public function getStudentCode(): ?string
    {
        return $this->studentCode;
    }

    public function setStudentCode(string $studentCode): static
    {
        $this->studentCode = $studentCode;

        return $this;
    }

    public function getCurrentClass(): ?ClassGroup
    {
        return $this->currentClass;
    }

    public function setCurrentClass(?ClassGroup $currentClass): static
    {
        $this->currentClass = $currentClass;

        return $this;
    }

    /**
     * @return Collection<int, Guardian>
     */
    public function getGuardians(): Collection
    {
        return $this->guardians;
    }

    public function addGuardian(Guardian $guardian): static
    {
        if (!$this->guardians->contains($guardian)) {
            $this->guardians->add($guardian);
        }

        return $this;
    }

    public function removeGuardian(Guardian $guardian): static
    {
        $this->guardians->removeElement($guardian);

        return $this;
    }

    /**
     * @return Collection<int, Attendance>
     */
    public function getAttendanceRecords(): Collection
    {
        return $this->attendanceRecords;
    }

    public function addAttendanceRecord(Attendance $attendanceRecord): static
    {
        if (!$this->attendanceRecords->contains($attendanceRecord)) {
            $this->attendanceRecords->add($attendanceRecord);
            $attendanceRecord->setStudent($this);
        }

        return $this;
    }

    public function removeAttendanceRecord(Attendance $attendanceRecord): static
    {
        if ($this->attendanceRecords->removeElement($attendanceRecord)) {
            // set the owning side to null (unless already changed)
            if ($attendanceRecord->getStudent() === $this) {
                $attendanceRecord->setStudent(null);
            }
        }

        return $this;
    }
}
