<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Définition des paramètres
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?int $registration_number = null;

    #[ORM\ManyToOne(inversedBy: 'doctors')]
    private ?Specialty $specialty = null;

    /**
     * @var Collection<int, Stay>
     */
    #[ORM\OneToMany(targetEntity: Stay::class, mappedBy: 'user')]
    private Collection $stays;

    /**
     * @var Collection<int, Stay>
     */
    #[ORM\OneToMany(targetEntity: Stay::class, mappedBy: 'doctor')]
    private Collection $doctor_stays;

    // Définition des getters et setters
    public function __construct()
    {
        $this->stays = new ArrayCollection();
        $this->doctor_stays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRegistrationNumber(): ?int
    {
        return $this->registration_number;
    }

    public function setRegistrationNumber(?int $registration_number): static
    {
        $this->registration_number = $registration_number;

        return $this;
    }

    public function getSpecialty(): ?Specialty
    {
        return $this->specialty;
    }

    public function setSpecialty(?Specialty $specialty): static
    {
        $this->specialty = $specialty;

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function addStay(Stay $stay): static
    {
        if (!$this->stays->contains($stay)) {
            $this->stays->add($stay);
            $stay->setUser($this);
        }

        return $this;
    }

    public function removeStay(Stay $stay): static
    {
        if ($this->stays->removeElement($stay)) {
            if ($stay->getUser() === $this) {
                $stay->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getDoctorStays(): Collection
    {
        return $this->doctor_stays;
    }

    public function addDoctorStay(Stay $doctorStay): static
    {
        if (!$this->doctor_stays->contains($doctorStay)) {
            $this->doctor_stays->add($doctorStay);
            $doctorStay->setDoctor($this);
        }

        return $this;
    }

    public function removeDoctorStay(Stay $doctorStay): static
    {
        if ($this->doctor_stays->removeElement($doctorStay)) {
            if ($doctorStay->getDoctor() === $this) {
                $doctorStay->setDoctor(null);
            }
        }

        return $this;
    }

    // Définition d'une chaine de caractères représentant l'instance
    public function __toString() {
        return $this->firstname . ' ' . $this->lastname;
    }
}
