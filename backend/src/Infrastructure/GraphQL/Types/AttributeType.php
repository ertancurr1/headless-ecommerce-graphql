<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Attribute\Attribute;
use App\Infrastructure\Repositories\AttributeRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Attribute Type
 */
class AttributeType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Attribute',
            'description' => 'A product attribute definition',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique attribute identifier',
                    'resolve' => fn(Attribute $attribute) => $attribute->getId(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Display name (e.g., "Color", "Size")',
                    'resolve' => fn(Attribute $attribute) => $attribute->getName(),
                ],
                'code' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Code identifier (e.g., "color", "size")',
                    'resolve' => fn(Attribute $attribute) => $attribute->getCode(),
                ],
                'type' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Attribute type: text or select',
                    'resolve' => fn(Attribute $attribute) => $attribute->getType(),
                ],
                'options' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                    'description' => 'Available options for select-type attributes',
                    'resolve' => function (Attribute $attribute) {
                        if (!$attribute->isSelectType()) {
                            return [];
                        }
                        if (empty($attribute->getOptions())) {
                            $repo = new AttributeRepository();
                            $options = $repo->loadOptions($attribute->getId());
                            $attribute->setOptions($options);
                        }
                        return array_map(
                            fn($option) => $option->getValue(),
                            $attribute->getOptions()
                        );
                    },
                ],
            ],
        ]);
    }
}