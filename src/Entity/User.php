<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Email sudah terdaftar!")
 * @UniqueEntity(fields={"username"}, message="Username sudah terdaftar!")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $namaLengkap;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $nomorHp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->enabled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNamaLengkap(string $namaLengkap): void
    {
        $this->namaLengkap = $namaLengkap;
    }

    public function getNamaLengkap(): ?string
    {
        return $this->namaLengkap;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setNomorHp(string $nomorHp): void
    {
        $this->nomorHp = $nomorHp;
    }

    public function getNomorHp(): ?string
    {
        return $this->nomorHp;
    }

    public function setConfirmationToken(string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setEnabled(?bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }
}
