<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Product\Product;
use App\Infrastructure\GraphQL\TypeRegistry;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\ProductRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Product Type
 * 
 * Defines the shape of Product data in GraphQL responses.
 */
class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'description' => 'A product in the catalog',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique product identifier',
                    'resolve' => fn(Product $product) => $product->getId(),
                ],
                'sku' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Stock Keeping Unit - unique product code',
                    'resolve' => fn(Product $product) => $product->getSku(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Product name',
                    'resolve' => fn(Product $product) => $product->getName(),
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'Product description',
                    'resolve' => fn(Product $product) => $product->getDescription(),
                ],
                'price' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'Regular price',
                    'resolve' => fn(Product $product) => $product->getPrice(),
                ],
                'specialPrice' => [
                    'type' => Type::float(),
                    'description' => 'Sale/discount price',
                    'resolve' => fn(Product $product) => $product->getSpecialPrice(),
                ],
                'effectivePrice' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'The actual price (special price if available, otherwise regular)',
                    'resolve' => fn(Product $product) => $product->getEffectivePrice(),
                ],
                'hasDiscount' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether product is on sale',
                    'resolve' => fn(Product $product) => $product->hasDiscount(),
                ],
                'discountPercentage' => [
                    'type' => Type::float(),
                    'description' => 'Discount percentage if on sale',
                    'resolve' => fn(Product $product) => $product->getDiscountPercentage(),
                ],
                'productType' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Product type: simple or configurable',
                    'resolve' => fn(Product $product) => $product->getProductType(),
                ],
                'stockQuantity' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Available stock quantity',
                    'resolve' => fn(Product $product) => $product->getStockQuantity(),
                ],
                'stockStatus' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Stock status: in_stock or out_of_stock',
                    'resolve' => fn(Product $product) => $product->getStockStatus(),
                ],
                'inStock' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether product is in stock',
                    'resolve' => fn(Product $product) => $product->isInStock(),
                ],
                'isActive' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether product is active/visible',
                    'resolve' => fn(Product $product) => $product->isActive(),
                ],
                'attributes' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::attributeValue()))),
                    'description' => 'Product attribute values',
                    'resolve' => function (Product $product) {
                        if (empty($product->getAttributeValues())) {
                            $repo = new ProductRepository();
                            $values = $repo->loadAttributeValues($product->getId());
                            $product->setAttributeValues($values);
                        }
                        return $product->getAttributeValues();
                    },
                ],
                'images' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::productImage()))),
                    'description' => 'Product images',
                    'resolve' => function (Product $product) {
                        if (empty($product->getImages())) {
                            $repo = new ProductRepository();
                            $images = $repo->loadImages($product->getId());
                            $product->setImages($images);
                        }
                        return $product->getImages();
                    },
                ],
                'categories' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::category()))),
                    'description' => 'Product categories',
                    'resolve' => function (Product $product) {
                        $repo = new CategoryRepository();
                        return $repo->findByProductId($product->getId());
                    },
                ],
                'variants' => [
                    'type' => Type::listOf(Type::nonNull(TypeRegistry::product())),
                    'description' => 'Variants for configurable products',
                    'resolve' => function (Product $product) {
                        if (!$product->isConfigurable()) {
                            return [];
                        }
                        $repo = new ProductRepository();
                        return $repo->findVariants($product->getId());
                    },
                ],
                'createdAt' => [
                    'type' => Type::string(),
                    'description' => 'Creation timestamp',
                    'resolve' => fn(Product $product) => $product->getCreatedAt()?->format('Y-m-d H:i:s'),
                ],
                'updatedAt' => [
                    'type' => Type::string(),
                    'description' => 'Last update timestamp',
                    'resolve' => fn(Product $product) => $product->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ],
            ],
        ]);
    }
}