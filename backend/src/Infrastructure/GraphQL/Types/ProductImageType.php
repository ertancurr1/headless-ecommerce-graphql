<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Product\ProductImage;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL ProductImage Type
 */
class ProductImageType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'ProductImage',
            'description' => 'A product gallery image',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique image identifier',
                    'resolve' => fn(ProductImage $image) => $image->getId(),
                ],
                'url' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Image URL',
                    'resolve' => fn(ProductImage $image) => $image->getUrl(),
                ],
                'altText' => [
                    'type' => Type::string(),
                    'description' => 'Alternative text for accessibility',
                    'resolve' => fn(ProductImage $image) => $image->getAltText(),
                ],
                'position' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Display order',
                    'resolve' => fn(ProductImage $image) => $image->getPosition(),
                ],
                'isPrimary' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether this is the main product image',
                    'resolve' => fn(ProductImage $image) => $image->isPrimary(),
                ],
            ],
        ]);
    }
}