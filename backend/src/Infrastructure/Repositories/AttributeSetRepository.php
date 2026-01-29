<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Attribute\AttributeSet;
use App\Shared\Entity\AbstractEntity;
use App\Shared\Repository\AbstractRepository;
use PDO;

/**
 * AttributeSet Repository
 * 
 * Handles all database operations for AttributeSet entities.
 */
class AttributeSetRepository extends AbstractRepository
{
    protected string $table = 'attribute_sets';
    protected string $entityClass = AttributeSet::class;

    private AttributeRepository $attributeRepository;

    public function __construct()
    {
        parent::__construct();
        $this->attributeRepository = new AttributeRepository();
    }

    /**
     * Find attribute set by name
     */
    public function findByName(string $name): ?AttributeSet
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
        
        $row = $stmt->fetch();
        
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Find attribute set with its attributes loaded
     */
    public function findByIdWithAttributes(int $id): ?AttributeSet
    {
        /** @var AttributeSet|null $attributeSet */
        $attributeSet = $this->findById($id);
        
        if ($attributeSet === null) {
            return null;
        }

        $attributes = $this->attributeRepository->findByAttributeSetIdWithOptions($id);
        $attributeSet->setAttributes($attributes);

        return $attributeSet;
    }

    /**
     * Get all attribute sets with their attributes
     *
     * @return array<AttributeSet>
     */
    public function findAllWithAttributes(): array
    {
        $attributeSets = $this->findAll();

        foreach ($attributeSets as $attributeSet) {
            /** @var AttributeSet $attributeSet */
            $attributes = $this->attributeRepository->findByAttributeSetIdWithOptions(
                $attributeSet->getId()
            );
            $attributeSet->setAttributes($attributes);
        }

        return $attributeSets;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbstractEntity $entity): AbstractEntity
    {
        /** @var AttributeSet $entity */
        $data = $this->extract($entity);

        if ($entity->exists()) {
            $this->update($entity->getId(), $data);
        } else {
            $id = $this->insert($data);
            $entity->setId($id);
        }

        return $entity;
    }

    private function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    private function update(int $id, array $data): void
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(array $row): AttributeSet
    {
        $attributeSet = new AttributeSet($row['name']);
        $attributeSet->setId((int) $row['id']);
        $attributeSet->setTimestamps($row['created_at'], $row['updated_at']);

        return $attributeSet;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract(AbstractEntity $entity): array
    {
        /** @var AttributeSet $entity */
        return [
            'name' => $entity->getName(),
        ];
    }
}
