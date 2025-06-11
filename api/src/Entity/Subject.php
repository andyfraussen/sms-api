<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['subject:read']],
    denormalizationContext: ['groups' => ['subject:write']],
)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['subject:read'])]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'subjects')]
    private Collection $teachers;

    #[ORM\Column(length: 255)]
    #[Groups(['subject:read', 'subject:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<int, ClassGroup>
     */
    #[ORM\ManyToMany(targetEntity: ClassGroup::class, inversedBy: 'subjects')]
    #[Groups(['subject:read', 'subject:write'])]
    private Collection $classGroups;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->classGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $teacher->addSubject($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): static
    {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeSubject($this);
        }

        return $this;
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
        }

        return $this;
    }

    public function removeClassGroup(ClassGroup $classGroup): static
    {
        $this->classGroups->removeElement($classGroup);

        return $this;
    }
}
