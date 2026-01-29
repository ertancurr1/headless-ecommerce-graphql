<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Infrastructure\Database\Connection;
use App\Shared\Entity\AbstractEntity;
use PDO;

/**
 * Abstract Repository
 * 
 * Provides common database operations for all repositories.
 * Child repositories must define the table name and entity class.
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * PDO instance
     */
    protected PDO $pdo;

    /**
     * Database table name
     */
    protected string $table;

    /**
     * Entity class name
     */
    protected string $entityClass;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?AbstractEntity
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     *  {@inheritdoc}
    */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        $entities = [];
        while ($row = $stmt->fetch()) {
            $entities[] = $this->hydrate($row);
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AbstractEntity $entity): bool
    {
        if (!$entity->exists()) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $entity->getId()]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Convert database row to entity object
     * 
     * @param array<string, mixed> $row
     * @return AbstractEntity
     */
    abstract protected function hydrate(array $row): AbstractEntity;

    /**
     * Convert entity to database row
     * 
     * @param AbstractEntity $entity
     * @return array<string, mixed>
     */
    abstract protected function extract(AbstractEntity $entity): array;
}    