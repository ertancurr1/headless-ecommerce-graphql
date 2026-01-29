<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Category\Category;
use App\Shared\Entity\AbstractEntity;
use App\Shared\Repository\AbstractRepository;
use PDO;

/**
 * Category Repository
 * 
 * Handles all database operations for Category entities.
 */
class CategoryRepository extends AbstractRepository
{
    protected string $table = 'categories';
    protected string $entityClass = Category::class;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        
        $row = $stmt->fetch();
        
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Find all root categories (no parent)
     *
     * @return array<Category>
     */
    public function findRootCategories(): array
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE parent_id IS NULL AND is_active = 1
            ORDER BY position ASC, name ASC
        ";

        $stmt = $this->pdo->query($sql);

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = $this->hydrate($row);
        }

        return $categories;
    }

    /**
     * Find children of a category
     *
     * @return array<Category>
     */
    public function findChildren(int $parentId): array
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE parent_id = :parent_id AND is_active = 1
            ORDER BY position ASC, name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['parent_id' => $parentId]);

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = $this->hydrate($row);
        }

        return $categories;
    }

    /**
     * Find all active categories
     *
     * @return array<Category>
     */
    public function findActive(): array
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE is_active = 1
            ORDER BY parent_id ASC, position ASC, name ASC
        ";

        $stmt = $this->pdo->query($sql);

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = $this->hydrate($row);
        }

        return $categories;
    }

    /**
     * Get full category tree (hierarchical structure)
     *
     * @return array<Category>
     */
    public function getCategoryTree(): array
    {
        // Get all active categories
        $allCategories = $this->findActive();
        
        // Index by ID for quick lookup
        $indexed = [];
        foreach ($allCategories as $category) {
            $indexed[$category->getId()] = $category;
        }

        // Build tree structure
        $tree = [];
        foreach ($allCategories as $category) {
            if ($category->getParentId() === null) {
                // Root category
                $tree[] = $category;
            } else {
                // Child category - add to parent
                $parentId = $category->getParentId();
                if (isset($indexed[$parentId])) {
                    $indexed[$parentId]->addChild($category);
                }
            }
        }

        return $tree;
    }

    /**
     * Get category with its children loaded
     */
    public function findByIdWithChildren(int $id): ?Category
    {
        /** @var Category|null $category */
        $category = $this->findById($id);
        
        if ($category === null) {
            return null;
        }

        $children = $this->findChildren($id);
        $category->setChildren($children);

        return $category;
    }

    /**
     * Get categories for a product
     *
     * @return array<Category>
     */
    public function findByProductId(int $productId): array
    {
        $sql = "
            SELECT c.* 
            FROM {$this->table} c
            INNER JOIN product_categories pc ON c.id = pc.category_id
            WHERE pc.product_id = :product_id AND c.is_active = 1
            ORDER BY c.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['product_id' => $productId]);

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = $this->hydrate($row);
        }

        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbstractEntity $entity): AbstractEntity
    {
        /** @var Category $entity */
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
    protected function hydrate(array $row): Category
    {
        $category = new Category(
            $row['name'],
            $row['slug'],
            $row['description'],
            $row['parent_id'] !== null ? (int) $row['parent_id'] : null
        );

        $category->setId((int) $row['id']);
        $category->setPosition((int) $row['position']);
        $category->setActive((bool) $row['is_active']);
        $category->setTimestamps($row['created_at'], $row['updated_at']);

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract(AbstractEntity $entity): array
    {
        /** @var Category $entity */
        return [
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'parent_id' => $entity->getParentId(),
            'position' => $entity->getPosition(),
            'is_active' => $entity->isActive() ? 1 : 0,
        ];
    }
}