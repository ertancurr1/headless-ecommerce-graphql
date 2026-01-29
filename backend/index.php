<?php

declare(strict_types=1);

/**
 * Application Entry Point
 * 
 * This is the main entry point for the backend API.
 * All requests are routed through this file.
 */

// Load bootstrap (environment, config, autoloader)
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

// Load configurations
$appConfig = require __DIR__ . '/config/app.php';
$dbConfig = require __DIR__ . '/config/database.php';

// For now, display configuration status (will be replaced with GraphQL endpoint)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appConfig['name']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #2d3748; }
        .status { padding: 15px; border-radius: 8px; margin: 10px 0; }
        .success { background: #c6f6d5; color: #276749; }
        .info { background: #bee3f8; color: #2b6cb0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f7fafc; }
        code { background: #edf2f7; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🚀 <?= htmlspecialchars($appConfig['name']) ?></h1>
    
    <div class="status success">
        ✅ Configuration loaded successfully!
    </div>

    <h2>Environment</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Environment</td>
            <td><code><?= htmlspecialchars($appConfig['environment']) ?></code></td>
        </tr>
        <tr>
            <td>Debug Mode</td>
            <td><code><?= $appConfig['debug'] ? 'true' : 'false' ?></code></td>
        </tr>
        <tr>
            <td>PHP Version</td>
            <td><code><?= PHP_VERSION ?></code></td>
        </tr>
    </table>

    <h2>Database Configuration</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Host</td>
            <td><code><?= htmlspecialchars($dbConfig['host']) ?></code></td>
        </tr>
        <tr>
            <td>Port</td>
            <td><code><?= $dbConfig['port'] ?></code></td>
        </tr>
        <tr>
            <td>Database</td>
            <td><code><?= htmlspecialchars($dbConfig['database']) ?></code></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><code><?= htmlspecialchars($dbConfig['username']) ?></code></td>
        </tr>
        <tr>
            <td>Charset</td>
            <td><code><?= htmlspecialchars($dbConfig['charset']) ?></code></td>
        </tr>
    </table>

    <div class="status info">
        ℹ️ This page will be replaced with the GraphQL endpoint in Phase 3.
    </div>
</body>
</html>
