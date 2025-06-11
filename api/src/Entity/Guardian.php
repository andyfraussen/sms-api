<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GuardianRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuardianRepository::class)]
#[ApiResource]
class Guardian extends Person
{

    /**
     * @var Collection<int, Student>
     */
    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'guardians')]
    private Collection $students;

    /**
     * @var Collection<int, School>
     */
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'guardians')]
    private Collection $schools;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->schools = new ArrayCollection();
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->addGuardian($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            $student->removeGuardian($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    public function addSchool(School $school): static
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
        }

        return $this;
    }

    public function removeSchool(School $school): static
    {
        $this->schools->removeElement($school);

        return $this;
    }
}
