<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

use App\Shared\Entity\AbstractEntity;
use InvalidArgumentException;

/**
 * Attribute Entity
 * 
 * Represents a dynamic product attribute (e.g., Size, Color, RAM).
 * Type 'select' has predefined options, 'text' allows free input.
 */
class Attribute extends AbstractEntity
{
    public const TYPE_TEXT = 'text';
    public const TYPE_SELECT = 'select';

    private string $name;
    private string $code;
    private string $type;

    /** @var array<AttributeOption> */
    private array $options = [];

    public function __construct(
        string $name,
        string $code,
        string $type = self::TYPE_TEXT,
    ) {
        $this->setName($name);
        $this->setCode($code);
        $this->setType($type);
    }

    // ==================== GETTERS ====================

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isSelectType(): bool
    {
        return $this->type === self::TYPE_SELECT;
    }

    public function isTextType(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    /** @return array<AttributeOption> */
    public function getOptions(): array
    {
        return $this->options;
    }

    // ==================== SETTERS ====================

    public function setName(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Attribute name cannot be empty');
        }

        $this->name = $name;
    }

    public function setCode(string $code): void
    {
        $code = trim($code);

        if (empty($code)) {
            throw new InvalidArgumentException('Attribute code cannot be empty');
        }

        // Code should be lowercase, alphanumeric with underscores
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $code)) {
            throw new InvalidArgumentException(
                'Attribute code must start with a letter and contain only lowercase letters, numbers, and underscores'
            );
        }

        $this->code = $code;
    }

    public function setType(string $type): void
    {
        $validTypes = [self::TYPE_TEXT, self::TYPE_SELECT];

        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid attribute type. Must be one of: %s', implode(', ', $validTypes))
            );
        }

        $this->type = $type;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function addOption(AttributeOption $option): void
    {
        $this->options[] = $option;
    }
}