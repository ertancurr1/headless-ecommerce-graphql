<?php

declare(strict_types=1);

/**
 * Application Entry Point
 */

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\AttributeSetRepository;

// Load configurations
$appConfig = require __DIR__ . '/config/app.php';

// Test repositories
$productRepo = new ProductRepository();
$categoryRepo = new CategoryRepository();
$attributeSetRepo = new AttributeSetRepository();

// Fetch data
$products = $productRepo->findActive(5);
$categories = $categoryRepo->getCategoryTree();
$attributeSets = $attributeSetRepo->findAllWithAttributes();

// Get a single product with relations
$productWithDetails = $productRepo->findByIdWithRelations(5);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appConfig['name']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
        h1 { color: #2d3748; }
        h2 { color: #4a5568; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .card { background: #f7fafc; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .success { background: #c6f6d5; color: #276749; padding: 10px; border-radius: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; }
        code { background: #edf2f7; padding: 2px 6px; border-radius: 4px; }
        .price { font-size: 1.2em; color: #2b6cb0; font-weight: bold; }
        .discount { color: #c53030; text-decoration: line-through; margin-right: 10px; }
        .attr { display: inline-block; background: #e2e8f0; padding: 3px 8px; border-radius: 4px; margin: 2px; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>🚀 <?= htmlspecialchars($appConfig['name']) ?></h1>
    
    <div class="success">
        ✅ Repositories working! Loaded <?= count($products) ?> products, <?= count($categories) ?> root categories, <?= count($attributeSets) ?> attribute sets.
    </div>

    <h2>📦 Products (First 5)</h2>
    <div class="grid">
        <?php foreach ($products as $product): ?>
        <div class="card">
            <strong><?= htmlspecialchars($product->getName()) ?></strong><br>
            <code><?= htmlspecialchars($product->getSku()) ?></code><br>
            <span class="price">
                <?php if ($product->hasDiscount()): ?>
                    <span class="discount">$<?= number_format($product->getPrice(), 2) ?></span>
                <?php endif; ?>
                $<?= number_format($product->getEffectivePrice(), 2) ?>
                <?php if ($product->hasDiscount()): ?>
                    <small>(<?= $product->getDiscountPercentage() ?>% off)</small>
                <?php endif; ?>
            </span><br>
            <small>Type: <?= $product->getProductType() ?> | Stock: <?= $product->getStockQuantity() ?></small>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($productWithDetails): ?>
    <h2>🔍 Product Details: <?= htmlspecialchars($productWithDetails->getName()) ?></h2>
    <div class="card">
        <p><strong>SKU:</strong> <?= htmlspecialchars($productWithDetails->getSku()) ?></p>
        <p><strong>Price:</strong> $<?= number_format($productWithDetails->getEffectivePrice(), 2) ?></p>
        <p><strong>Attributes:</strong></p>
        <?php foreach ($productWithDetails->getAttributeValues() as $attr): ?>
            <span class="attr"><?= htmlspecialchars($attr->getAttributeName()) ?>: <?= htmlspecialchars($attr->getValue()) ?></span>
        <?php endforeach; ?>
        <p><strong>Images:</strong> <?= count($productWithDetails->getImages()) ?></p>
    </div>
    <?php endif; ?>

    <h2>📂 Category Tree</h2>
    <?php foreach ($categories as $rootCategory): ?>
    <div class="card">
        <strong><?= htmlspecialchars($rootCategory->getName()) ?></strong>
        <?php if ($rootCategory->hasChildren()): ?>
        <ul>
            <?php foreach ($rootCategory->getChildren() as $child): ?>
            <li><?= htmlspecialchars($child->getName()) ?> <code><?= $child->getSlug() ?></code></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <h2>🏷️ Attribute Sets</h2>
    <?php foreach ($attributeSets as $set): ?>
    <div class="card">
        <strong><?= htmlspecialchars($set->getName()) ?></strong><br>
        <?php foreach ($set->getAttributes() as $attr): ?>
            <span class="attr">
                <?= htmlspecialchars($attr->getName()) ?> 
                (<?= $attr->getType() ?>)
                <?php if ($attr->isSelectType() && count($attr->getOptions()) > 0): ?>
                    : <?= implode(', ', array_map(fn($o) => $o->getValue(), $attr->getOptions())) ?>
                <?php endif; ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</body>
</html>