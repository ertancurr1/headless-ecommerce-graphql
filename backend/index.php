<?php

declare(strict_types=1);

/**
 * Application Entry Point
 */

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

$appConfig = require __DIR__ . '/config/app.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appConfig['name']) ?></title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px;
            background: #f7fafc;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 { color: #2d3748; margin-bottom: 10px; }
        .subtitle { color: #718096; margin-bottom: 30px; }
        .endpoint {
            background: #edf2f7;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            margin-right: 10px;
            margin-top: 10px;
        }
        .btn:hover { background: #3182ce; }
        .btn-secondary { background: #48bb78; }
        .btn-secondary:hover { background: #38a169; }
        h2 { color: #4a5568; margin-top: 30px; }
        code { background: #edf2f7; padding: 2px 6px; border-radius: 4px; }
        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 <?= htmlspecialchars($appConfig['name']) ?></h1>
        <p class="subtitle">GraphQL API is ready!</p>
        
        <h2>📡 GraphQL Endpoint</h2>
        <div class="endpoint">
            POST http://localhost/headless-ecommerce-graphql/backend/graphql.php
        </div>

        <a href="https://studio.apollographql.com/sandbox/explorer" target="_blank" class="btn">
            Open Apollo Studio
        </a>
        <a href="https://altairgraphql.dev/" target="_blank" class="btn btn-secondary">
            Download Altair Client
        </a>

        <h2>📝 Example Queries</h2>
        
        <h3>Get All Products</h3>
        <pre>{
  products(limit: 5) {
    id
    name
    sku
    price
    effectivePrice
    hasDiscount
    inStock
  }
}</pre>

        <h3>Get Single Product with Details</h3>
        <pre>{
  product(id: 5) {
    id
    name
    sku
    price
    description
    attributes {
      attributeName
      value
    }
    images {
      url
      isPrimary
    }
    categories {
      name
      slug
    }
  }
}</pre>

        <h3>Filter Products by Price</h3>
        <pre>{
  products(filter: { minPrice: 50, maxPrice: 200 }) {
    name
    price
  }
}</pre>

        <h3>Get Category Tree</h3>
        <pre>{
  categoryTree {
    id
    name
    slug
    children {
      id
      name
      slug
    }
  }
}</pre>

        <h3>Get Attribute Sets</h3>
        <pre>{
  attributeSets {
    id
    name
    attributes {
      name
      code
      type
      options
    }
  }
}</pre>

    </div>
</body>
</html>