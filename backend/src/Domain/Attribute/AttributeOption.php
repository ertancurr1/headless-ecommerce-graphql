<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * AttributeOption Entity
 * 
 * Represents a predefined option for a 'select' type attribute.
 * Example: Size attribute has options S, M, L, XL.
 */
class AttributeOption extends AbstractEntity
{
    private int $attributeId;
    private string $value;
    private int $displayOrder;

    public function __construct(
        int $attributeId,
        string $value,
        int $displayOrder = 0
    ) {
        $this->attributeId = $attributeId;
        $this->setValue($value);
        $this->setDisplayOrder($displayOrder);
    }

    // ==================== GETTERS ====================

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    // ==================== SETTERS ====================

    public function setValue(string $value): void
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('Option value cannot be empty');
        }

        $this->value = $value;
    }

    public function setDisplayOrder(int $displayOrder): void
    {
        $this->displayOrder = $displayOrder;
    }
}