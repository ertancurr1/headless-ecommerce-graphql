<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Product;
use App\Domain\Product\AttributeValue;
use App\Domain\Product\ProductImage;
use App\Shared\Entity\AbstractEntity;
use App\Shared\Repository\AbstractRepository;
use PDO;

/**
 * Product Repository
 * 
 * Handles all database operations for Product entities.
 */
class ProductRepository extends AbstractRepository
{
    protected string $table = 'products';
    protected string $entityClass = Product::class;

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product
    {
        $sql = "SELECT * FROM {$this->table} WHERE sku = :sku LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['sku' => $sku]);
        
        $row = $stmt->fetch();
        
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Find all active products
     *
     * @return array<Product>
     */
    public function findActive(?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    /**
     * Find products by category ID
     *
     * @return array<Product>
     */
    public function findByCategory(int $categoryId, ?int $limit = null, int $offset = 0): array
    {
        $sql = "
            SELECT p.* 
            FROM {$this->table} p
            INNER JOIN product_categories pc ON p.id = pc.product_id
            WHERE pc.category_id = :category_id 
            AND p.is_active = 1
            ORDER BY p.created_at DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    /**
     * Find products with filters
     *
     * @param array<string, mixed> $filters
     * @return array<Product>
     */
    public function findWithFilters(array $filters = [], ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT DISTINCT p.* FROM {$this->table} p";
        $joins = [];
        $where = ['p.is_active = 1'];
        $params = [];

        // Category filter
        if (!empty($filters['category_id'])) {
            $joins[] = "INNER JOIN product_categories pc ON p.id = pc.product_id";
            $where[] = "pc.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        // Product type filter
        if (!empty($filters['product_type'])) {
            $where[] = "p.product_type = :product_type";
            $params['product_type'] = $filters['product_type'];
        }

        // Stock status filter
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_status = 'in_stock'";
        }

        // Attribute value filter (e.g., color=Black)
        if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
            foreach ($filters['attributes'] as $index => $attr) {
                $alias = "pav{$index}";
                $joins[] = "INNER JOIN product_attribute_values {$alias} ON p.id = {$alias}.product_id";
                $where[] = "{$alias}.attribute_id = :attr_id_{$index} AND {$alias}.value = :attr_val_{$index}";
                $params["attr_id_{$index}"] = $attr['attribute_id'];
                $params["attr_val_{$index}"] = $attr['value'];
            }
        }

        // Build final SQL
        $sql .= ' ' . implode(' ', $joins);
        $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY p.created_at DESC';

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($limit !== null) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    /**
     * Count products with filters
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(DISTINCT p.id) as count FROM {$this->table} p";
        $joins = [];
        $where = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $joins[] = "INNER JOIN product_categories pc ON p.id = pc.product_id";
            $where[] = "pc.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        $sql .= ' ' . implode(' ', $joins);
        $sql .= ' WHERE ' . implode(' AND ', $where);

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();

        return (int) $result['count'];
    }

    /**
     * Get product with all related data (attributes, categories, images)
     */
    public function findByIdWithRelations(int $id): ?Product
    {
        /** @var Product|null $product */
        $product = $this->findById($id);
        
        if ($product === null) {
            return null;
        }

        // Load attribute values
        $product->setAttributeValues($this->loadAttributeValues($id));
        
        // Load images
        $product->setImages($this->loadImages($id));
        
        // Load category IDs (full categories loaded separately if needed)
        
        return $product;
    }

    /**
     * Load attribute values for a product
     *
     * @return array<AttributeValue>
     */
    public function loadAttributeValues(int $productId): array
    {
        $sql = "
            SELECT pav.*, a.name as attribute_name, a.code as attribute_code
            FROM product_attribute_values pav
            INNER JOIN attributes a ON pav.attribute_id = a.id
            WHERE pav.product_id = :product_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['product_id' => $productId]);

        $values = [];
        while ($row = $stmt->fetch()) {
            $value = new AttributeValue(
                (int) $row['product_id'],
                (int) $row['attribute_id'],
                $row['value']
            );
            $value->setId((int) $row['id']);
            $value->setAttributeName($row['attribute_name']);
            $value->setAttributeCode($row['attribute_code']);
            $values[] = $value;
        }

        return $values;
    }

    /**
     * Load images for a product
     *
     * @return array<ProductImage>
     */
    public function loadImages(int $productId): array
    {
        $sql = "
            SELECT * FROM product_images 
            WHERE product_id = :product_id 
            ORDER BY position ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['product_id' => $productId]);

        $images = [];
        while ($row = $stmt->fetch()) {
            $image = new ProductImage(
                (int) $row['product_id'],
                $row['url'],
                $row['alt_text'],
                (int) $row['position'],
                (bool) $row['is_primary']
            );
            $image->setId((int) $row['id']);
            $images[] = $image;
        }

        return $images;
    }

    /**
     * Get variants for a configurable product
     *
     * @return array<Product>
     */
    public function findVariants(int $parentProductId): array
    {
        $sql = "
            SELECT p.* 
            FROM {$this->table} p
            INNER JOIN product_variants pv ON p.id = pv.variant_product_id
            WHERE pv.parent_product_id = :parent_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['parent_id' => $parentProductId]);

        $variants = [];
        while ($row = $stmt->fetch()) {
            $variants[] = $this->hydrate($row);
        }

        return $variants;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbstractEntity $entity): AbstractEntity
    {
        /** @var Product $entity */
        $data = $this->extract($entity);

        if ($entity->exists()) {
            // Update
            $this->update($entity->getId(), $data);
        } else {
            // Insert
            $id = $this->insert($data);
            $entity->setId($id);
        }

        return $entity;
    }

    /**
     * Insert new product
     */
    private function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update existing product
     */
    private function update(int $id, array $data): void
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(array $row): Product
    {
        $product = new Product(
            $row['sku'],
            $row['name'],
            (float) $row['price'],
            (int) $row['attribute_set_id'],
            $row['product_type'],
            $row['description']
        );

        $product->setId((int) $row['id']);
        $product->setSpecialPrice(
            $row['special_price'] !== null ? (float) $row['special_price'] : null
        );
        $product->setStockQuantity((int) $row['stock_quantity']);
        $product->setStockStatus($row['stock_status']);
        $product->setActive((bool) $row['is_active']);
        $product->setTimestamps($row['created_at'], $row['updated_at']);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract(AbstractEntity $entity): array
    {
        /** @var Product $entity */
        return [
            'sku' => $entity->getSku(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'price' => $entity->getPrice(),
            'special_price' => $entity->getSpecialPrice(),
            'product_type' => $entity->getProductType(),
            'attribute_set_id' => $entity->getAttributeSetId(),
            'stock_quantity' => $entity->getStockQuantity(),
            'stock_status' => $entity->getStockStatus(),
            'is_active' => $entity->isActive() ? 1 : 0,
        ];
    }
}