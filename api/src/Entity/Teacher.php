<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ApiResource]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, School>
     */
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'teachers')]
    private Collection $schools;

    /**
     * @var Collection<int, ClassGroup>
     */
    #[ORM\ManyToMany(targetEntity: ClassGroup::class, inversedBy: 'teachers')]
    private Collection $classes;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'teachers')]
    private Collection $subjects;

    #[ORM\OneToOne(mappedBy: 'teacherProfile', cascade: ['persist', 'remove'])]
    private ?User $userProfile = null;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->subjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(ClassGroup $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
        }

        return $this;
    }

    public function removeClass(ClassGroup $class): static
    {
        $this->classes->removeElement($class);

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

    public function getUserProfile(): ?User
    {
        return $this->userProfile;
    }

    public function setUserProfile(?User $userProfile): static
    {
        // unset the owning side of the relation if necessary
        if ($userProfile === null && $this->userProfile !== null) {
            $this->userProfile->setTeacherProfile(null);
        }

        // set the owning side of the relation if necessary
        if ($userProfile !== null && $userProfile->getTeacherProfile() !== $this) {
            $userProfile->setTeacherProfile($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }
}
