<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection Singleton
 * 
 * Provides a single PDO instance throughout the application.
 * Uses the Singleton pattern to prevent multiple connections.
 */
final class Connection
{
    /**
     * The single PDO instance
     */
    private static ?PDO $instance = null;

    /**
     * Prevent direct instantiation
     */
    private function __construct()
    {

    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {

    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }

    /**
     * Get the PDO instance
     * 
     * Creates the connection on first call, returns existing on subsequent calls.
     * 
     * @return PDO
     * @throws RuntimeException If connection fails
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Create a new PDO connection
     * 
     * @return PDO
     * @throws RuntimeException if connection fails
     */
    private static function createConnection(): PDO
    {
        // Load database configuration
        $config = require APP_ROOT . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );

            return $pdo;
        } catch (PDOException $e) {
            // Don't expose credentials in error messages
            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage()
            );
        }
    }

    /**
     * Close the connection
     * 
     * Useful for testing or long-running scripts.
     */
    public static function disconnect(): void
    {
        self::$instance = null;
    }
}