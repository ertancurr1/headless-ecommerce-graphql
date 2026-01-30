<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types;

use App\Domain\Category\Category;
use App\Infrastructure\GraphQL\TypeRegistry;
use App\Infrastructure\Repositories\CategoryRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Category Type
 */
class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'description' => 'A product category',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'Unique category identifier',
                    'resolve' => fn(Category $category) => $category->getId(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Category name',
                    'resolve' => fn(Category $category) => $category->getName(),
                ],
                'slug' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'URL-friendly category identifier',
                    'resolve' => fn(Category $category) => $category->getSlug(),
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'Category description',
                    'resolve' => fn(Category $category) => $category->getDescription(),
                ],
                'parentId' => [
                    'type' => Type::id(),
                    'description' => 'Parent category ID (null for root categories)',
                    'resolve' => fn(Category $category) => $category->getParentId(),
                ],
                'position' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Sort order within parent',
                    'resolve' => fn(Category $category) => $category->getPosition(),
                ],
                'isActive' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether category is visible',
                    'resolve' => fn(Category $category) => $category->isActive(),
                ],
                'isRoot' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether this is a root category',
                    'resolve' => fn(Category $category) => $category->isRoot(),
                ],
                'path' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Full category path (e.g., "Electronics > Smartphones")',
                    'resolve' => fn(Category $category) => $category->getPath(),
                ],
                'parent' => [
                    'type' => TypeRegistry::category(),
                    'description' => 'Parent category',
                    'resolve' => function (Category $category) {
                        if ($category->getParentId() === null) {
                            return null;
                        }
                        $repo = new CategoryRepository();
                        return $repo->findById($category->getParentId());
                    },
                ],
                'children' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::category()))),
                    'description' => 'Child categories',
                    'resolve' => function (Category $category) {
                        $repo = new CategoryRepository();
                        return $repo->findChildren($category->getId());
                    },
                ],
            ],
        ]);
    }
}