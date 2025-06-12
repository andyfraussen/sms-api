<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'person' => Person::class,
    'teacher' => Teacher::class,
    'student' => Student::class,
    'guardian' => Guardian::class,
])]
class Person
{
    #[ORM\OneToOne(inversedBy: 'person', cascade: ['persist', 'remove'])]
    #[Groups(['teacher:read', 'teacher:write'])]
    #[Assert\Valid]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['teacher:read'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        if ($user && $user->getPerson() !== $this) {
            $user->setPerson($this);
        }

        return $this;
    }
}
