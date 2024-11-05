<?php

namespace Api\Traits;
use Doctrine\ORM\Mapping as ORM;
use Api\Entity\User;

trait UserEntityTrait
{
    use DateTimeEntityTrait;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted = false;

    #[ORM\ManyToOne(targetEntity: 'Api\Entity\User')]
    private ?User $createdBy;

    #[ORM\ManyToOne(targetEntity: 'Api\Entity\User')]
    private ?User $updatedBy;

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     */
    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User|null $updatedBy
     */
    public function setUpdatedBy(?User $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}