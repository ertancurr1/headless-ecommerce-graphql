<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Attribute\AttributeSet;
use App\Infrastructure\GraphQL\TypeRegistry;
use App\Infrastructure\Repositories\AttributeRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL AttributeSet Type
 */
class AttributeSetType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeSet',
            'description' => 'A set of attributes for a product type',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique attribute set identifier',
                    'resolve' => fn(AttributeSet $set) => $set->getId(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Attribute set name (e.g., "Clothing", "Electronics")',
                    'resolve' => fn(AttributeSet $set) => $set->getName(),
                ],
                'attributes' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::attribute()))),
                    'description' => 'Attributes in this set',
                    'resolve' => function (AttributeSet $set) {
                        if (empty($set->getAttributes())) {
                            $repo = new AttributeRepository();
                            $attributes = $repo->findByAttributeSetIdWithOptions($set->getId());
                            $set->setAttributes($attributes);
                        }
                        return $set->getAttributes();
                    },
                ],
            ],
        ]);
    }
}