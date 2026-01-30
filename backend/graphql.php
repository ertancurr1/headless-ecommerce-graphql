<?php

declare(strict_types=1);

/**
 * GraphQL API Endpoint
 * 
 * All GraphQL requests are handled through this file.
 * Accepts POST requests with JSON body containing query and variables.
 */

// Load bootstrap and autoloader
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\GraphQL\Schema;
use GraphQL\GraphQL;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Get input
    $rawInput = file_get_contents('php://input');
    
    if ($rawInput === false || $rawInput === '') {
        // Handle GET requests (for GraphQL playground/testing)
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
            $input = [
                'query' => $_GET['query'],
                'variables' => isset($_GET['variables']) ? json_decode($_GET['variables'], true) : null,
            ];
        } else {
            throw new RuntimeException('No GraphQL query provided');
        }
    } else {
        $input = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON: ' . json_last_error_msg());
        }
    }

    // Extract query and variables
    $query = $input['query'] ?? '';
    $variables = $input['variables'] ?? null;
    $operationName = $input['operationName'] ?? null;

    // Validate query exists
    if (empty($query)) {
        throw new RuntimeException('No GraphQL query provided');
    }

    // Build schema
    $schema = Schema::build();

    // Configure debug flags based on environment
    $debugFlag = env('GRAPHQL_DEBUG', false)
        ? DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE
        : DebugFlag::NONE;

    // Execute query
    $result = GraphQL::executeQuery(
        $schema,
        $query,
        null,           // Root value
        null,           // Context (can pass user auth info here)
        $variables,
        $operationName
    );

    // Format output
    $output = $result->toArray($debugFlag);

} catch (Throwable $e) {
    // Handle errors
    $output = [
        'errors' => [
            FormattedError::createFromException($e)
        ]
    ];
    
    // Add debug info in development
    if (env('GRAPHQL_DEBUG', false)) {
        $output['errors'][0]['trace'] = $e->getTraceAsString();
    }

    http_response_code(400);
}

// Output JSON response
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);