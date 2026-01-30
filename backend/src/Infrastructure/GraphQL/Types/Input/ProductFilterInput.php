<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL Input Type for Product Filtering
 */
class ProductFilterInput extends InputObjectType
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct([
            'name' => 'ProductFilterInput',
            'description' => 'Filter criteria for products',
            'fields' => [
                'categoryId' => [
                    'type' => Type::id(),
                    'description' => 'Filter by category ID',
                ],
                'minPrice' => [
                    'type' => Type::float(),
                    'description' => 'Minimum price',
                ],
                'maxPrice' => [
                    'type' => Type::float(),
                    'description' => 'Maximum price',
                ],
                'productType' => [
                    'type' => Type::string(),
                    'description' => 'Filter by product type (simple/configurable)',
                ],
                'inStock' => [
                    'type' => Type::boolean(),
                    'description' => 'Filter to only in-stock products',
                ],
            ],
        ]);
    }
}