<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[ORM\UniqueConstraint(columns: ['name'])]
#[UniqueEntity(fields: ['name'], errorPath: 'name', ignoreNull: true)]
#[ApiResource(
    operations: [
        new Post(),
        new Get(),
        new GetCollection(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['school:read']],
    denormalizationContext: ['groups' => ['school:write']],
    order: ['name' => 'ASC']
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'name' => 'ipartial',
])]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['school:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['school:read', 'school:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<int, Grade>
     */
    #[ORM\OneToMany(targetEntity: Grade::class, mappedBy: 'school')]
    #[Groups(['school:read'])]
    private Collection $grades;

    /**
     * @var Collection<int, ClassGroup>
     */
    #[ORM\OneToMany(targetEntity: ClassGroup::class, mappedBy: 'school')]
    private Collection $classGroups;

    /**
     * @var Collection<int, Teacher>
     */
    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'schools')]
    private Collection $teachers;

    /**
     * @var Collection<int, Guardian>
     */
    #[ORM\ManyToMany(targetEntity: Guardian::class, mappedBy: 'schools')]
    private Collection $guardians;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->classGroups = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->guardians = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setSchool($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getSchool() === $this) {
                $grade->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClassGroup>
     */
    public function getClassGroups(): Collection
    {
        return $this->classGroups;
    }

    public function addClassGroup(ClassGroup $classGroup): static
    {
        if (!$this->classGroups->contains($classGroup)) {
            $this->classGroups->add($classGroup);
            $classGroup->setSchool($this);
        }

        return $this;
    }

    public function removeClassGroup(ClassGroup $classGroup): static
    {
        if ($this->classGroups->removeElement($classGroup)) {
            // set the owning side to null (unless already changed)
            if ($classGroup->getSchool() === $this) {
                $classGroup->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Teacher>
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
            $teacher->addSchool($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): static
    {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeSchool($this);
        }

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
            $guardian->addSchool($this);
        }

        return $this;
    }

    public function removeGuardian(Guardian $guardian): static
    {
        if ($this->guardians->removeElement($guardian)) {
            $guardian->removeSchool($this);
        }

        return $this;
    }
}
