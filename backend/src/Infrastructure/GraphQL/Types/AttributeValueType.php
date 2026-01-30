<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Product\AttributeValue;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL AttributeValue Type
 */
class AttributeValueType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeValue',
            'description' => 'An attribute value assigned to a product',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique value identifier',
                    'resolve' => fn(AttributeValue $av) => $av->getId(),
                ],
                'attributeId' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The attribute this value belongs to',
                    'resolve' => fn(AttributeValue $av) => $av->getAttributeId(),
                ],
                'attributeName' => [
                    'type' => Type::string(),
                    'description' => 'Attribute display name',
                    'resolve' => fn(AttributeValue $av) => $av->getAttributeName(),
                ],
                'attributeCode' => [
                    'type' => Type::string(),
                    'description' => 'Attribute code',
                    'resolve' => fn(AttributeValue $av) => $av->getAttributeCode(),
                ],
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The actual value',
                    'resolve' => fn(AttributeValue $av) => $av->getValue(),
                ],
            ],
        ]);
    }
}