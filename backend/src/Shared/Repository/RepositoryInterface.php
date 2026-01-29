<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\AbstractEntity;

/**
 * Base Repository Interface
 * 
 * Defines the contract for all repositories.
 * Each entity repository will extend this with specific methods.
 */
interface RepositoryInterface
{
    /**
     * Find entity by ID
     * 
     * @param int $id
     * @return AbstractEntity|null
     */
    public function findById(int $id): ?AbstractEntity;

    /**
     * Find all entities
     * 
     * @param int|null $limit
     * @param int|null $offset
     * @return array<AbstractEntity>
     */
    public function findAll(?int $limit = null, int $offset = 0): array;

    /**
     * Save entity (insert or update)
     * 
     * @param AbstractEntity $entity
     * @return AbstractEntity
     */    
    public function save(AbstractEntity $entity): AbstractEntity;

    /**
     * Delete entity
     * 
     * @param AbstractEntity $entity
     * @return bool
     */
    public function delete(AbstractEntity $entity): bool;

    /**
     * Count all entities
     * 
     * @return int
     */
    public function count(): int;
}