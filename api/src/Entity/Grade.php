<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['grade:read']],
    denormalizationContext: ['groups' => ['grade:write']]
)]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['grade:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['grade:read', 'grade:write'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['grade:read', 'grade:write'])]
    private ?School $school = null;

    /**
     * @var Collection<int, ClassGroup>
     */
    #[ORM\OneToMany(targetEntity: ClassGroup::class, mappedBy: 'grade')]
    private Collection $classGroups;

    public function __construct()
    {
        $this->classGroups = new ArrayCollection();
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

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

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
            $classGroup->setGrade($this);
        }

        return $this;
    }

    public function removeClassGroup(ClassGroup $classGroup): static
    {
        if ($this->classGroups->removeElement($classGroup)) {
            // set the owning side to null (unless already changed)
            if ($classGroup->getGrade() === $this) {
                $classGroup->setGrade(null);
            }
        }

        return $this;
    }
}
