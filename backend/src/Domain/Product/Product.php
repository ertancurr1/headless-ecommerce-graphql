<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * Product Entity
 * 
 * Represents a product in the e-commerce system.
 * Can be either 'simple' (single variant) or 'configurable' (has variants).
 */
class Product extends AbstractEntity
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_CONFIGURABLE = 'configurable';

    public const STOCK_IN_STOCK = 'in_stock';
    public const STOCK_OUT_OF_STOCK = 'out_of_stock';

    private string $sku;
    private string $name;
    private ?string $description;
    private float $price;
    private ?float $specialPrice;
    private string $productType;
    private int $attributeSetId;
    private int $stockQuantity;
    private string $stockStatus;
    private bool $isActive;

    /** @var array<AttributeValue> */
    private array $attributeValues = [];

    /** @var array<Category> */    
    private array $categories = [];

    /** @var array<ProductImage> */
    private array $images = [];

    /** @var array<Product> Variants for configurable products */    
    private array $variants = [];

    /**
     * Create a new Product
     */    
    public function __construct(
        string $sku,
        string $name,
        float $price,
        int $attributeSetId,
        string $productType = self::TYPE_SIMPLE,
        ?string $description = null,
    ) {
        $this->setSku($sku);
        $this->setName($name);
        $this->setPrice($price);
        $this->attributeSetId = $attributeSetId;
        $this->setProductType($productType);
        $this->description = $description;
        $this->specialPrice = null;
        $this->stockQuantity = 0;
        $this->stockStatus = self::STOCK_IN_STOCK;
        $this->isActive = true;
    }

    // ==================== GETTERS ====================

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }

    /**
     * Get the effective price (special price if available, otherwise regular price)
     */
    public function getEffectivePrice(): float
    {
        return $this->specialPrice ?? $this->price;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getAttributeSetId(): int
    {
        return $this->attributeSetId;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }

    public function getStockStatus(): string
    {
        return $this->stockStatus;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isSimple(): bool
    {
        return $this->productType === self::TYPE_SIMPLE;
    }

    public function isConfigurable(): bool
    {
        return $this->productType === self::TYPE_CONFIGURABLE;
    }

    public function isInStock(): bool
    {
        return $this->stockStatus === self::STOCK_IN_STOCK;
    }

    /** 
     * Check if product has a discount
     */
    public function hasDiscount(): bool
    {
        return $this->specialPrice !== null && $this->specialPrice < $this->price;
    }

    /**
     *  Get discount percentage
     */
    public function getDiscountPercentage(): ?float
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return round((($this->price - $this->specialPrice) / $this->price) * 100, 1);
    }

    // ==================== SETTERS ====================

    public function setSku(string $sku): void
    {
        $sku = trim($sku);

        if (empty($sku)) {
            throw new InvalidArgumentException('SKU cannot be empty');
        }

        if (strlen($sku) > 100) {
            throw new InvalidArgumentException('SKU cannot exceed 100 characters');
        }

        $this->sku = $sku;
    }

    public function setName(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new InvalidArgumentException('Product name cannot exceed 255 characters');
        }

        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description !== null ? trim($description) : null;
    }

    public function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }

        $this->price = round($price, 2);
    }

    public function setSpecialPrice(?float $specialPrice): void
    {
        if ($specialPrice !== null) {
            if ($specialPrice < 0) {
                throw new InvalidArgumentException('Special price cannot be negative');
            }
            $specialPrice = round($specialPrice, 2);
        }

        $this->specialPrice = $specialPrice;
    }

    public function setProductType(string $productType): void
    {
        $validTypes = [self::TYPE_SIMPLE, self::TYPE_CONFIGURABLE];

        if (!in_array($productType, $validTypes, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid product type. Must be one of: $s', implode(', ', $validTypes))
            );
        }

        $this->productType = $productType;
    }

    public function setAttributeSetId(int $attributeSetId): void
    {
        if ($attributeSetId <= 0) {
            throw new InvalidArgumentException('Attribute set ID must be positive');
        }

        $this->attributeSetId = $attributeSetId;
    }

    public function setStockQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Stock quantity cannot be negative');
        }

        $this->stockQuantity = $quantity;

        // Auto-update stock status
        $this->stockStatus = $quantity > 0
            ? self::STOCK_IN_STOCK
            : self::STOCK_OUT_OF_STOCK;
    }

    public function setStockStatus(string $status): void
    {
        $validStatuses = [self::STOCK_IN_STOCK, self::STOCK_OUT_OF_STOCK];

        if (!in_array($status, $validStatuses, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid stock status. Must be one of: %s', implode(', ', $validStatuses))
            );
        }

        $this->stockStatus = $status;
    }

    public function setActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * @return array<AttributeValue>
     */
    public function getAttributeValues(): array
    {
        return $this->attributeValues;
    }

    public function setAttributeValues(array $attributeValues): void
    {
        $this->attributeValues = $attributeValues;
    }

    public function addAttributeValue(AttributeValue $attributeValue): void
    {
        $this->attributeValues[] = $attributeValue;
    }

    /** 
     * @return array<Category>
    */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return array<ProductImage>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * Get primary image
     */
    public function getPrimaryImage(): ?ProductImage
    {
        foreach ($this->images as $image) {
            if ($image->isPrimary()) {
                return $image;
            }
        }

        return $this->images[0] ?? null;
    }

    /**
     * @return array<Product>
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }
}