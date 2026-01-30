<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL;

use App\Infrastructure\GraphQL\Types\Input\ProductFilterInput;
use App\Infrastructure\Repositories\AttributeSetRepository;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\ProductRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Root Query Type
 * 
 * Defines all available read operations.
 */
class QueryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Query',
            'description' => 'Root query operations',
            'fields' => [
                // ==================== PRODUCTS ====================
                'products' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::product()))),
                    'description' => 'Get all products with optional filtering',
                    'args' => [
                        'filter' => [
                            'type' => ProductFilterInput::getInstance(),
                            'description' => 'Filter criteria',
                        ],
                        'limit' => [
                            'type' => Type::int(),
                            'description' => 'Maximum number of products to return',
                            'defaultValue' => 20,
                        ],
                        'offset' => [
                            'type' => Type::int(),
                            'description' => 'Number of products to skip (for pagination)',
                            'defaultValue' => 0,
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new ProductRepository();
                        
                        $filters = [];
                        if (isset($args['filter'])) {
                            if (isset($args['filter']['categoryId'])) {
                                $filters['category_id'] = (int) $args['filter']['categoryId'];
                            }
                            if (isset($args['filter']['minPrice'])) {
                                $filters['min_price'] = $args['filter']['minPrice'];
                            }
                            if (isset($args['filter']['maxPrice'])) {
                                $filters['max_price'] = $args['filter']['maxPrice'];
                            }
                            if (isset($args['filter']['productType'])) {
                                $filters['product_type'] = $args['filter']['productType'];
                            }
                            if (isset($args['filter']['inStock']) && $args['filter']['inStock']) {
                                $filters['in_stock'] = true;
                            }
                        }

                        if (empty($filters)) {
                            return $repo->findActive($args['limit'], $args['offset']);
                        }

                        return $repo->findWithFilters($filters, $args['limit'], $args['offset']);
                    },
                ],

                'product' => [
                    'type' => TypeRegistry::product(),
                    'description' => 'Get a single product by ID',
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                            'description' => 'Product ID',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new ProductRepository();
                        return $repo->findByIdWithRelations((int) $args['id']);
                    },
                ],

                'productBySku' => [
                    'type' => TypeRegistry::product(),
                    'description' => 'Get a single product by SKU',
                    'args' => [
                        'sku' => [
                            'type' => Type::nonNull(Type::string()),
                            'description' => 'Product SKU',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new ProductRepository();
                        return $repo->findBySku($args['sku']);
                    },
                ],

                'productsCount' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Get total count of products (with optional filter)',
                    'args' => [
                        'filter' => [
                            'type' => ProductFilterInput::getInstance(),
                            'description' => 'Filter criteria',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new ProductRepository();
                        
                        $filters = [];
                        if (isset($args['filter'])) {
                            if (isset($args['filter']['categoryId'])) {
                                $filters['category_id'] = (int) $args['filter']['categoryId'];
                            }
                            if (isset($args['filter']['minPrice'])) {
                                $filters['min_price'] = $args['filter']['minPrice'];
                            }
                            if (isset($args['filter']['maxPrice'])) {
                                $filters['max_price'] = $args['filter']['maxPrice'];
                            }
                        }

                        if (empty($filters)) {
                            return $repo->count();
                        }

                        return $repo->countWithFilters($filters);
                    },
                ],

                // ==================== CATEGORIES ====================
                'categories' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::category()))),
                    'description' => 'Get all categories (flat list)',
                    'resolve' => function () {
                        $repo = new CategoryRepository();
                        return $repo->findActive();
                    },
                ],

                'categoryTree' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::category()))),
                    'description' => 'Get categories as hierarchical tree (root categories with children)',
                    'resolve' => function () {
                        $repo = new CategoryRepository();
                        return $repo->getCategoryTree();
                    },
                ],

                'category' => [
                    'type' => TypeRegistry::category(),
                    'description' => 'Get a single category by ID',
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                            'description' => 'Category ID',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new CategoryRepository();
                        return $repo->findByIdWithChildren((int) $args['id']);
                    },
                ],

                'categoryBySlug' => [
                    'type' => TypeRegistry::category(),
                    'description' => 'Get a single category by slug',
                    'args' => [
                        'slug' => [
                            'type' => Type::nonNull(Type::string()),
                            'description' => 'Category slug (URL-friendly name)',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new CategoryRepository();
                        return $repo->findBySlug($args['slug']);
                    },
                ],

                // ==================== ATTRIBUTE SETS ====================
                'attributeSets' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::attributeSet()))),
                    'description' => 'Get all attribute sets with their attributes',
                    'resolve' => function () {
                        $repo = new AttributeSetRepository();
                        return $repo->findAllWithAttributes();
                    },
                ],

                'attributeSet' => [
                    'type' => TypeRegistry::attributeSet(),
                    'description' => 'Get a single attribute set by ID',
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                            'description' => 'Attribute set ID',
                        ],
                    ],
                    'resolve' => function ($root, array $args) {
                        $repo = new AttributeSetRepository();
                        return $repo->findByIdWithAttributes((int) $args['id']);
                    },
                ],
            ],
        ]);
    }
}