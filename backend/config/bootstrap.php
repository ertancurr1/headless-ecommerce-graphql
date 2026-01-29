<?php

declare(strict_types=1);

/**
 * Application Bootstrap
 * 
 * Loads environment variables and initializes configuration.
 * This file should be included at the entry point of the application.
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

/**
 * Load environment variables from .env file
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new RuntimeException(
            ".env file not found at: {$path}\n" .
            "Please copy .env.example to .env and configure your settings."
        );
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Parse KEY=value pairs
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set environment variable if not already set
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

/**
 * Get environment variable with optional default
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key) ?: $default;
    
    // Convert string booleans
    if (is_string($value)) {
        $lowered = strtolower($value);
        if ($lowered === 'true') return true;
        if ($lowered === 'false') return false;
        if ($lowered === 'null') return null;
    }
    
    return $value;
}

// Load environment variables
loadEnv(APP_ROOT . '/.env');

// Set error reporting based on environment
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set default timezone
date_default_timezone_set('UTC');