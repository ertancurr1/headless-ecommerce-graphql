<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * Category Entity
 * 
 * Represents a product category with hierarchical structure.
 * Categories can be nested (parent/child relationships).
 */
class Category extends AbstractEntity
{
    private string $name;
    private string $slug;
    private ?string $description;
    private ?int $parentId;
    private int $position;
    private bool $isActive;

    /** @var array<Category> */
    private array $children = [];

    /** @var Category|null */
    private ?Category $parent = null;

    public function __construct(
        string $name,
        string $slug,
        ?string $description = null,
        ?int $parentId = null,
    ) {
        $this->setName($name);
        $this->setSlug($slug);
        $this->description = $description;
        $this->parentId = $parentId;
        $this->position = 0;
        $this->isActive = true;
    }

    // ==================== GETTERS ====================

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isRoot(): bool
    {
        return $this->parentId === null;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * @return array<Category>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * Get full category path (e.g., "Electronics > Smartphones")
     */
    public function getPath(): string
    {
        if ($this->parent === null) {
            return $this->name;
        }

        return $this->parent->getPath() . ' > ' . $this->name;
    }

    // ==================== SETTERS ====================

    public function setName(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Category name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new InvalidArgumentException('Category name cannot exceed 255 characters');
        }

        $this->name = $name;
    }

    public function setSlug(string $slug): void
    {
        $slug = trim($slug);

        if (empty($slug)) {
            throw new InvalidArgumentException('Category slug cannot be empty');
        }

        // Validate slug format (lowercase, hyphens, no spaces)
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new InvalidArgumentException(
                'Slug must be lowercase alphanumeric with hyphens only'
            );
        }

        $this->slug = $slug;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description !== null ? trim($description) : null;
    }

    public function setParentId(?int $parentId): void
    {
        // Prevent category from being its own parent
        if ($parentId !== null && $this->id !== null && $parentId === $this->id) {
            throw new InvalidArgumentException('Category cannot be its own parent');
        }

        $this->parentId = $parentId;
    }

    public function setPosition(int $position): void
    {
        if ($position < 0) {
            throw new InvalidArgumentException('Position cannot be negative');
        }

        $this->position = $position;
    }

    public function setActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    // ==================== RELATIONSHIPS ====================

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function addChild(Category $child): void
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
        $this->parentId = $parent?->getId();
    }
}