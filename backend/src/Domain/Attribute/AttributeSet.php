<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * AttributeSet Entity
 * 
 * Groups attributes for different product types.
 * Example: "Clothing" set has Size, Color, Material attributes.
 */
class AttributeSet extends AbstractEntity
{
    private string $name;

    /** @var array<Attribute> */
    private array $attributes = [];

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    // ==================== GETTERS ====================

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    // ==================== SETTERS ====================

    public function setName(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Attribute set name cannot be empty');
        }

        $this->name = $name;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }
}