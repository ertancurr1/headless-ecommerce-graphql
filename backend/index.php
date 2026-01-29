<?php

declare(strict_types=1);

/**
 * Backend Entry Point - Test File
 * This file verifies that Apache and PHP are working correctly.
 * Will be replaced with the actual GraphQL endpoint later.
 */

// Display PHP configuration for verification
echo "<h1>🚀 Backend is Working!</h1>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Verify required PHP extensions for our project
echo "<h2>Required Extensions Check:</h2>";
echo "<ul>";

$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];

foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "<li>{$status} {$ext}</li>";
}

echo "</ul>";