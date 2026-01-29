<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * AttributeValue Entity (EAV Pattern)
 * 
 * Stores the actual attribute value for a specific product.
 * Example: Product "iPhone 15" has AttributeValue (Storage ="128GB")..
 */
class AttributeValue extends AbstractEntity
{
    private int $productId;
    private int $attributeId;
    private string $value;

    /** @var string|null Attribute name (loaded from join) */
    private ?string $attributeName = null;

    /** @var string|null Attribute code (loaded from join) */
    private ?string $attributeCode = null;

    public function __construct(
        int $productId,
        int $attributeId,
        string $value
    ) {
        $this->productId = $productId;
        $this->attributeId = $attributeId;
        $this->setValue($value);
    }

    // ==================== GETTERS ====================

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAttributeName(): ?string
    {
        return $this->attributeName;
    }

    public function getAttributeCode(): ?string
    {
        return $this->attributeCode;
    }

    // ==================== SETTERS ====================

    public function setValue(string $value): void
    {
        $this->value = trim($value);
    }

    public function setAttributeName(?string $name): void
    {
        $this->attributeName = $name;
    }

    public function setAttributeCode(?string $code): void
    {
        $this->attributeCode = $code;
    }
}