-- =============================================
-- Headless E-Commerce Database Schema
-- Migration: 001_initial_schema
-- Description: Creates all initial tables
-- =============================================

-- Use the database
USE headless_ecommerce;

-- =============================================
-- TABLE: attribute_sets
-- Groups of attributes for product types
-- Example: "Clothing" has size, color
--          "Electronics" has storage, RAM
-- =============================================
CREATE TABLE attribute_sets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_attribute_sets_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: attributes
-- Dynamic product properties
-- type: 'text' for free input, 'select' for predefined options
-- =============================================
CREATE TABLE attributes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL COMMENT 'URL-friendly identifier (e.g., "color", "size")',
    type ENUM('text', 'select') NOT NULL DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_attributes_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: attribute_options
-- Predefined values for 'select' type attributes
-- Example: Size attribute has options: S, M, L, XL
-- =============================================
CREATE TABLE attribute_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attribute_id INT UNSIGNED NOT NULL,
    value VARCHAR(255) NOT NULL,
    display_order INT UNSIGNED DEFAULT 0,
    
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    UNIQUE KEY uk_attribute_options (attribute_id, value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: attribute_set_items
-- Links attributes to attribute sets (many-to-many)
-- =============================================
CREATE TABLE attribute_set_items (
    attribute_set_id INT UNSIGNED NOT NULL,
    attribute_id INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (attribute_set_id, attribute_id),
    FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: categories
-- Hierarchical product categories (tree structure)
-- parent_id = NULL means root category
-- =============================================
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL COMMENT 'URL-friendly name',
    description TEXT,
    parent_id INT UNSIGNED DEFAULT NULL,
    position INT UNSIGNED DEFAULT 0 COMMENT 'Sort order within parent',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_categories_slug (slug),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: products
-- Main product table
-- product_type: 'simple' or 'configurable'
-- =============================================
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) NOT NULL COMMENT 'Stock Keeping Unit - unique product identifier',
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    special_price DECIMAL(10, 2) DEFAULT NULL COMMENT 'Sale price',
    product_type ENUM('simple', 'configurable') NOT NULL DEFAULT 'simple',
    attribute_set_id INT UNSIGNED NOT NULL,
    stock_quantity INT UNSIGNED DEFAULT 0,
    stock_status ENUM('in_stock', 'out_of_stock') DEFAULT 'in_stock',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_products_sku (sku),
    FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id),
    INDEX idx_products_type (product_type),
    INDEX idx_products_price (price),
    INDEX idx_products_active (is_active),
    INDEX idx_products_stock (stock_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: product_images
-- Product gallery images
-- =============================================
CREATE TABLE product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    position INT UNSIGNED DEFAULT 0 COMMENT 'Display order',
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_images_product (product_id),
    INDEX idx_product_images_primary (product_id, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: product_attribute_values
-- EAV: Stores actual attribute values for products
-- =============================================
CREATE TABLE product_attribute_values (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    attribute_id INT UNSIGNED NOT NULL,
    value TEXT NOT NULL,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    UNIQUE KEY uk_product_attribute (product_id, attribute_id),
    INDEX idx_pav_attribute (attribute_id),
    INDEX idx_pav_value (attribute_id, value(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: product_categories
-- Many-to-many: Products belong to multiple categories
-- =============================================
CREATE TABLE product_categories (
    product_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_pc_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: configurable_product_options
-- Links configurable products to their variant-defining attributes
-- Example: A T-Shirt is configurable by "Size" and "Color"
-- =============================================
CREATE TABLE configurable_product_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL COMMENT 'The configurable (parent) product',
    attribute_id INT UNSIGNED NOT NULL COMMENT 'The attribute used for variants',
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    UNIQUE KEY uk_config_product_attribute (product_id, attribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- TABLE: product_variants
-- Links configurable products to their simple product variants
-- =============================================
CREATE TABLE product_variants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_product_id INT UNSIGNED NOT NULL COMMENT 'The configurable product',
    variant_product_id INT UNSIGNED NOT NULL COMMENT 'The simple product variant',
    
    FOREIGN KEY (parent_product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uk_variant (parent_product_id, variant_product_id),
    INDEX idx_variant_parent (parent_product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
