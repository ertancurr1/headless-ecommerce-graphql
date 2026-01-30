<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL;

use GraphQL\Type\Schema as GraphQLSchema;
use GraphQL\Type\SchemaConfig;

/**
 * GraphQL Schema Factory
 * 
 * Creates and configures the complete GraphQL schema.
 */
final class Schema
{
    private static ?GraphQLSchema $instance = null;

    /**
     * Get the GraphQL schema instance
     */
    public static function build(): GraphQLSchema
    {
        if (self::$instance === null) {
            self::$instance = new GraphQLSchema(
                SchemaConfig::create()
                    ->setQuery(new QueryType())
                    // Future: ->setMutation(new MutationType())
            );
        }

        return self::$instance;
    }

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }
}