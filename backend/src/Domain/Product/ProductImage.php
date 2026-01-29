<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * ProductImage Entity
 * 
 * Represents an image in a product's gallery.
 */
class ProductImage extends AbstractEntity
{
    private int $productId;
    private string $url;
    private ?string $altText;
    private int $position;
    private bool $isPrimary;

    public function __construct(
        int $productId,
        string $url,
        ?string $altText = null,
        int $position = 0,
        bool $isPrimary = false
    ) {
        $this->productId = $productId;
        $this->setUrl($url);
        $this->altText = $altText;
        $this->position = $position;
        $this->isPrimary = $isPrimary;
    }

    // ==================== GETTERS ====================

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    // ==================== SETTERS ====================

    public function setUrl(string $url): void
    {
        $url = trim($url);

        if (empty($url)) {
            throw new InvalidArgumentException('Image URL cannot be empty');
        }

        $this->url = $url;
    }

    public function setAltText(?string $altText): void
    {
        $this->altText = $altText !== null ? trim($altText) : null;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }
}