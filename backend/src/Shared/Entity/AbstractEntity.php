<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use DateTimeImmutable;

/**
 * Base Entity Class
 * 
 * All domain entities extend this class.
 * Provides common properties and behavior.
 */
abstract class AbstractEntity
{
    /**
     * Entity identifier
     */
    protected ?int $id = null;

    /**
     * Creation timestamp
     */
    protected ?DateTimeImmutable $createdAt = null;

    /**
     * Last update timestamp
     */
    protected ?DateTimeImmutable $updatedAt = null;

    /**
     * Get the entity ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Check if entity exists in database (has ID)
     */
    public function exists(): bool
    {
        return $this->id !== null;
    }

    /**
     * Get creation timestamp
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Get last update timestamp
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
    
    /**
     * Set timestamps from database values
     * 
     * @internal Used by repositories when hydrating entities
     */
    public function setTimestamps(?string $createdAt, ?string $updatedAt): void
    {
        $this->createdAt = $createdAt 
            ? new DateTimeImmutable($createdAt) 
            : null;
        $this->updatedAt = $updatedAt 
            ? new DateTimeImmutable($updatedAt) 
            : null;
    }

    /**
     * Set the entity ID
     * 
     * @internal Used by repositories after insert
     */    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}