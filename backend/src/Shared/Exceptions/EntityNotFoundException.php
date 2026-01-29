<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;

/**
 * Exception thrown when an entity is not found
 */
class EntityNotFoundException extends RuntimeException
{
    /**
     * Create exception for entity not found by ID
     */
    public static function forId(string $entityName, int $id): self
    {
        return new self(
            sprintf('%s with ID %d not found', $entityName, $id)
        );
    }

    /**
     * Create exception for entity not found by field
     */
    public static function forField(string $entityName, string $field, mixed $value): self
    {
        return new self(
            sprintf('%s with %s "%s" not found', $entityName, $field, $value)
        );
    }
}