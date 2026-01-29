<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeOption;
use App\Shared\Entity\AbstractEntity;
use App\Shared\Repository\AbstractRepository;
use PDO;

/**
 * Attribute Repository
 * 
 * Handles all database operations for Attribute entities.
 */
class AttributeRepository extends AbstractRepository
{
    protected string $table = 'attributes';
    protected string $entityClass = Attribute::class;

    /**
     * Find attribute by code
     */
    public function findByCode(string $code): ?Attribute
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        
        $row = $stmt->fetch();
        
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Find attributes by attribute set ID
     *
     * @return array<Attribute>
     */
    public function findByAttributeSetId(int $attributeSetId): array
    {
        $sql = "
            SELECT a.* 
            FROM {$this->table} a
            INNER JOIN attribute_set_items asi ON a.id = asi.attribute_id
            WHERE asi.attribute_set_id = :attribute_set_id
            ORDER BY a.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['attribute_set_id' => $attributeSetId]);

        $attributes = [];
        while ($row = $stmt->fetch()) {
            $attributes[] = $this->hydrate($row);
        }

        return $attributes;
    }

    /**
     * Find attribute with its options loaded
     */
    public function findByIdWithOptions(int $id): ?Attribute
    {
        /** @var Attribute|null $attribute */
        $attribute = $this->findById($id);
        
        if ($attribute === null) {
            return null;
        }

        if ($attribute->isSelectType()) {
            $options = $this->loadOptions($id);
            $attribute->setOptions($options);
        }

        return $attribute;
    }

    /**
     * Find attributes by set ID with options loaded
     *
     * @return array<Attribute>
     */
    public function findByAttributeSetIdWithOptions(int $attributeSetId): array
    {
        $attributes = $this->findByAttributeSetId($attributeSetId);

        foreach ($attributes as $attribute) {
            /** @var Attribute $attribute */
            if ($attribute->isSelectType()) {
                $options = $this->loadOptions($attribute->getId());
                $attribute->setOptions($options);
            }
        }

        return $attributes;
    }

    /**
     * Load options for an attribute
     *
     * @return array<AttributeOption>
     */
    public function loadOptions(int $attributeId): array
    {
        $sql = "
            SELECT * FROM attribute_options 
            WHERE attribute_id = :attribute_id
            ORDER BY display_order ASC, value ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['attribute_id' => $attributeId]);

        $options = [];
        while ($row = $stmt->fetch()) {
            $option = new AttributeOption(
                (int) $row['attribute_id'],
                $row['value'],
                (int) $row['display_order']
            );
            $option->setId((int) $row['id']);
            $options[] = $option;
        }

        return $options;
    }

    /**
     * Get unique attribute values used in products (for filters)
     *
     * @return array<string>
     */
    public function getUsedValues(int $attributeId, ?int $categoryId = null): array
    {
        $sql = "
            SELECT DISTINCT pav.value
            FROM product_attribute_values pav
            INNER JOIN products p ON pav.product_id = p.id
        ";

        $params = ['attribute_id' => $attributeId];

        if ($categoryId !== null) {
            $sql .= " INNER JOIN product_categories pc ON p.id = pc.product_id";
            $sql .= " WHERE pav.attribute_id = :attribute_id AND pc.category_id = :category_id AND p.is_active = 1";
            $params['category_id'] = $categoryId;
        } else {
            $sql .= " WHERE pav.attribute_id = :attribute_id AND p.is_active = 1";
        }

        $sql .= " ORDER BY pav.value ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $values = [];
        while ($row = $stmt->fetch()) {
            $values[] = $row['value'];
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbstractEntity $entity): AbstractEntity
    {
        /** @var Attribute $entity */
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
    protected function hydrate(array $row): Attribute
    {
        $attribute = new Attribute(
            $row['name'],
            $row['code'],
            $row['type']
        );

        $attribute->setId((int) $row['id']);
        $attribute->setTimestamps($row['created_at'], $row['updated_at']);

        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract(AbstractEntity $entity): array
    {
        /** @var Attribute $entity */
        return [
            'name' => $entity->getName(),
            'code' => $entity->getCode(),
            'type' => $entity->getType(),
        ];
    }
}