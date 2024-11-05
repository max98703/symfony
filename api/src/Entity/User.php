<?php

namespace Api\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Api\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * class Project
 * 
 * Defines the properties of the Project entity to represent the Project.
 * See https://symfony.com/doc/current/doctrine.html#creating-an-entity-class.
 * 
 * We are using Project to validate the object properties.
 * 
 * Please make sure that you have run to run this command
 * ``php bin/console doctrine:schema:update -f --complete``
 *  before running application
 * 
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotNull]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.',)]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;
    
    #[Assert\NotNull]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $username;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[Ignore]
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $status = true;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $roles;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', nullable: true)]
    private $resetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $resetPasswordRequestTime;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $resetFlag = 0;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private $deleted = false;

    #[ORM\ManyToOne(targetEntity: 'Api\Entity\User')]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: 'Api\Entity\User')]
    private $updatedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): string
    {
        return $this->roles;
    }

    public function setRoles( $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }
    public function getUserName()
    {
        return $this->username;
    }
    /**
     * @param mixed $name
     */
    public function setUserName($username)
    {
        $this->name = $username;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * @param null $resetToken
     */
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @return mixed
     */
    public function getResetPasswordRequestTime()
    {
        return $this->resetPasswordRequestTime;
    }

    /**
     * @param mixed $resetPasswordRequestTime
     */
    public function setResetPasswordRequestTime($resetPasswordRequestTime)
    {
        $this->resetPasswordRequestTime = $resetPasswordRequestTime;
    }

    /**
     * @return int
     */
    public function getResetFlag()
    {
        return $this->resetFlag;
    }

    /**
     * @param int $resetFlag
     */
    public function setResetFlag($resetFlag)
    {
        $this->resetFlag = $resetFlag;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

}