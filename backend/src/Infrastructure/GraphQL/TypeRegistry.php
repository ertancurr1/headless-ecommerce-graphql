<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL;

use App\Infrastructure\GraphQL\Types\AttributeSetType;
use App\Infrastructure\GraphQL\Types\AttributeType;
use App\Infrastructure\GraphQL\Types\AttributeValueType;
use App\Infrastructure\GraphQL\Types\CategoryType;
use App\Infrastructure\GraphQL\Types\ProductImageType;
use App\Infrastructure\GraphQL\Types\ProductType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Type Registry
 * 
 * Manages singleton instances of all GraphQL types.
 * Prevents circular reference issues and ensures type reuse.
*/
final class TypeRegistry
{
    private static ?ProductType $product = null;
    private static ?CategoryType $category = null;
    private static ?AttributeType $attribute = null;
    private static ?AttributeSetType $attributeSet = null;
    private static ?AttributeValueType $attributeValue = null;
    private static ?ProductImageType $productImage = null;

    // Scalar types (built-in)
    public static function string(): \GraphQL\Type\Definition\StringType
    {
        return Type::string();
    }

    public static function int(): \GraphQL\Type\Definition\IntType
    {
        return Type::int();
    }

    public static function float(): \GraphQL\Type\Definition\FloatType
    {
        return Type::float();
    }

    public static function boolean(): \GraphQL\Type\Definition\BooleanType
    {
        return Type::boolean();
    }

    public static function id(): \GraphQL\Type\Definition\IDType
    {
        return Type::id();
    }

    // Custom types
    public static function product(): ProductType
    {
        if (self::$product === null) {
            self::$product = new ProductType();
        }
        return self::$product;
    }

    public static function category(): CategoryType
    {
        if (self::$category === null) {
            self::$category = new CategoryType();
        }
        return self::$category;
    }

    public static function attribute(): AttributeType
    {
        if (self::$attribute === null) {
            self::$attribute = new AttributeType();
        }
        return self::$attribute;
    }

    public static function attributeSet(): AttributeSetType
    {
        if (self::$attributeSet === null) {
            self::$attributeSet = new AttributeSetType();
        }
        return self::$attributeSet;
    }

    public static function attributeValue(): AttributeValueType
    {
        if (self::$attributeValue === null) {
            self::$attributeValue = new AttributeValueType();
        }
        return self::$attributeValue;
    }

    public static function productImage(): ProductImageType
    {
        if (self::$productImage === null) {
            self::$productImage = new ProductImageType();
        }
        return self::$productImage;
    }

    // Helper for non-null types
    public static function nonNull($type): \GraphQL\Type\Definition\NonNull
    {
        return Type::nonNull($type);
    }

    // Helper for list types
    public static function listOf($type): \GraphQL\Type\Definition\ListOfType
    {
        return Type::listOf($type);
    }
}