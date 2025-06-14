<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['teacher:read']]
    ,denormalizationContext: ['groups' => ['teacher:write']],
    validationContext: ['groups' => ['Default']]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'user.email' => 'exact',
])]
class Teacher extends Person
{
    /**
     * @var Collection<int, School>
     */
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'teachers')]
    #[Groups(['teacher:read', 'teacher:write'])]
    private Collection $schools;

    /**
     * @var Collection<int, ClassGroup>
     */
    #[ORM\ManyToMany(targetEntity: ClassGroup::class, inversedBy: 'teachers')]
    #[Groups(['teacher:read', 'teacher:write'])]
    private Collection $classGroups;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'teachers')]
    #[Groups(['teacher:read', 'teacher:write'])]
    private Collection $subjects;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->classGroups = new ArrayCollection();
        $this->subjects = new ArrayCollection();
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

    /**
     * @return Collection<int, ClassGroup>
     */
    public function getClassGroups(): Collection
    {
        return $this->classGroups;
    }

    public function addClassGroup(ClassGroup $class): static
    {
        if (!$this->classGroups->contains($class)) {
            $this->classGroups->add($class);
        }

        return $this;
    }

    public function removeClassGroup(ClassGroup $class): static
    {
        $this->classGroups->removeElement($class);

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subjects->removeElement($subject);

        return $this;
    }
}
