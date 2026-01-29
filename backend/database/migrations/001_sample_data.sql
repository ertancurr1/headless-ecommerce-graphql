-- =============================================
-- Headless E-Commerce Sample Data
-- Seed: 001_sample_data
-- Description: Populates database with sample data
-- =============================================

USE headless_ecommerce;

-- =============================================
-- ATTRIBUTE SETS
-- =============================================
INSERT INTO attribute_sets (name) VALUES
    ('Clothing'),
    ('Electronics'),
    ('Books');


-- =============================================
-- ATTRIBUTES
-- =============================================
INSERT INTO attributes (name, code, type) VALUES
    -- Clothing attributes
    ('Size', 'size', 'select'),
    ('Color', 'color', 'select'),
    ('Material', 'material', 'select'),
    
    -- Electronics attributes
    ('Storage', 'storage', 'select'),
    ('RAM', 'ram', 'select'),
    ('Screen Size', 'screen_size', 'select'),
    ('Brand', 'brand', 'select'),
    
    -- Book attributes
    ('Author', 'author', 'text'),
    ('ISBN', 'isbn', 'text'),
    ('Pages', 'pages', 'text'),
    ('Publisher', 'publisher', 'text');


-- =============================================
-- ATTRIBUTE OPTIONS
-- =============================================

-- Size options (attribute_id = 1)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (1, 'XS', 1),
    (1, 'S', 2),
    (1, 'M', 3),
    (1, 'L', 4),
    (1, 'XL', 5),
    (1, 'XXL', 6);

-- Color options (attribute_id = 2)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (2, 'Black', 1),
    (2, 'White', 2),
    (2, 'Red', 3),
    (2, 'Blue', 4),
    (2, 'Green', 5),
    (2, 'Navy', 6),
    (2, 'Gray', 7);

-- Material options (attribute_id = 3)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (3, 'Cotton', 1),
    (3, 'Polyester', 2),
    (3, 'Wool', 3),
    (3, 'Silk', 4),
    (3, 'Linen', 5);

-- Storage options (attribute_id = 4)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (4, '64GB', 1),
    (4, '128GB', 2),
    (4, '256GB', 3),
    (4, '512GB', 4),
    (4, '1TB', 5);

-- RAM options (attribute_id = 5)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (5, '4GB', 1),
    (5, '8GB', 2),
    (5, '16GB', 3),
    (5, '32GB', 4),
    (5, '64GB', 5);

-- Screen Size options (attribute_id = 6)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (6, '5.5"', 1),
    (6, '6.1"', 2),
    (6, '6.7"', 3),
    (6, '13"', 4),
    (6, '14"', 5),
    (6, '15.6"', 6),
    (6, '27"', 7);

-- Brand options (attribute_id = 7)
INSERT INTO attribute_options (attribute_id, value, display_order) VALUES
    (7, 'Apple', 1),
    (7, 'Samsung', 2),
    (7, 'Sony', 3),
    (7, 'LG', 4),
    (7, 'Dell', 5),
    (7, 'HP', 6);


-- =============================================
-- ATTRIBUTE SET ITEMS (link attributes to sets)
-- =============================================

-- Clothing set (id=1) gets: Size, Color, Material
INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES
    (1, 1),  -- Size
    (1, 2),  -- Color
    (1, 3);  -- Material

-- Electronics set (id=2) gets: Storage, RAM, Screen Size, Brand, Color
INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES
    (2, 4),  -- Storage
    (2, 5),  -- RAM
    (2, 6),  -- Screen Size
    (2, 7),  -- Brand
    (2, 2);  -- Color (shared with clothing)

-- Books set (id=3) gets: Author, ISBN, Pages, Publisher
INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES
    (3, 8),   -- Author
    (3, 9),   -- ISBN
    (3, 10),  -- Pages
    (3, 11);  -- Publisher


-- =============================================
-- CATEGORIES (hierarchical)
-- =============================================
INSERT INTO categories (name, slug, description, parent_id, position, is_active) VALUES
    -- Root categories
    ('Men', 'men', 'Men\'s Fashion', NULL, 1, TRUE),
    ('Women', 'women', 'Women\'s Fashion', NULL, 2, TRUE),
    ('Electronics', 'electronics', 'Electronic Devices & Gadgets', NULL, 3, TRUE),
    ('Books', 'books', 'Books & Literature', NULL, 4, TRUE);

-- Subcategories (Men) - parent_id = 1
INSERT INTO categories (name, slug, description, parent_id, position, is_active) VALUES
    ('T-Shirts', 'men-tshirts', 'Men\'s T-Shirts', 1, 1, TRUE),
    ('Jeans', 'men-jeans', 'Men\'s Jeans', 1, 2, TRUE),
    ('Jackets', 'men-jackets', 'Men\'s Jackets', 1, 3, TRUE);

-- Subcategories (Women) - parent_id = 2
INSERT INTO categories (name, slug, description, parent_id, position, is_active) VALUES
    ('Dresses', 'women-dresses', 'Women\'s Dresses', 2, 1, TRUE),
    ('Tops', 'women-tops', 'Women\'s Tops', 2, 2, TRUE),
    ('Skirts', 'women-skirts', 'Women\'s Skirts', 2, 3, TRUE);

-- Subcategories (Electronics) - parent_id = 3
INSERT INTO categories (name, slug, description, parent_id, position, is_active) VALUES
    ('Smartphones', 'smartphones', 'Mobile Phones & Smartphones', 3, 1, TRUE),
    ('Laptops', 'laptops', 'Laptop Computers', 3, 2, TRUE),
    ('Tablets', 'tablets', 'Tablet Devices', 3, 3, TRUE);

-- Subcategories (Books) - parent_id = 4
INSERT INTO categories (name, slug, description, parent_id, position, is_active) VALUES
    ('Fiction', 'fiction', 'Fiction Books', 4, 1, TRUE),
    ('Non-Fiction', 'non-fiction', 'Non-Fiction Books', 4, 2, TRUE),
    ('Technology', 'tech-books', 'Technology & Programming Books', 4, 3, TRUE);


-- =============================================
-- PRODUCTS
-- =============================================

-- SIMPLE PRODUCTS (Clothing)
INSERT INTO products (sku, name, description, price, special_price, product_type, attribute_set_id, stock_quantity, stock_status, is_active) VALUES
    ('TSH-BLK-M', 'Classic Black T-Shirt', 'A comfortable classic black t-shirt made from 100% cotton.', 29.99, NULL, 'simple', 1, 100, 'in_stock', TRUE),
    ('TSH-WHT-M', 'Classic White T-Shirt', 'A comfortable classic white t-shirt made from 100% cotton.', 29.99, 24.99, 'simple', 1, 85, 'in_stock', TRUE),
    ('JNS-BLU-32', 'Slim Fit Blue Jeans', 'Modern slim fit jeans in classic blue wash.', 79.99, NULL, 'simple', 1, 50, 'in_stock', TRUE),
    ('JKT-BLK-L', 'Leather Jacket', 'Premium black leather jacket with classic design.', 199.99, 179.99, 'simple', 1, 25, 'in_stock', TRUE);

-- SIMPLE PRODUCTS (Electronics)
INSERT INTO products (sku, name, description, price, special_price, product_type, attribute_set_id, stock_quantity, stock_status, is_active) VALUES
    ('IPH-15-128', 'iPhone 15', 'Apple iPhone 15 with A16 Bionic chip.', 999.99, NULL, 'simple', 2, 200, 'in_stock', TRUE),
    ('IPH-15-256', 'iPhone 15', 'Apple iPhone 15 with A16 Bionic chip.', 1099.99, NULL, 'simple', 2, 150, 'in_stock', TRUE),
    ('SAM-S24-128', 'Samsung Galaxy S24', 'Samsung Galaxy S24 with advanced AI features.', 899.99, 849.99, 'simple', 2, 175, 'in_stock', TRUE),
    ('MBP-14-512', 'MacBook Pro 14"', 'Apple MacBook Pro 14-inch with M3 Pro chip.', 1999.99, NULL, 'simple', 2, 50, 'in_stock', TRUE),
    ('DELL-XPS-15', 'Dell XPS 15', 'Dell XPS 15 with Intel Core i7 and 16GB RAM.', 1499.99, 1399.99, 'simple', 2, 40, 'in_stock', TRUE);

-- SIMPLE PRODUCTS (Books)
INSERT INTO products (sku, name, description, price, special_price, product_type, attribute_set_id, stock_quantity, stock_status, is_active) VALUES
    ('BK-CLEAN-CODE', 'Clean Code', 'A Handbook of Agile Software Craftsmanship by Robert C. Martin.', 44.99, NULL, 'simple', 3, 500, 'in_stock', TRUE),
    ('BK-PRAG-PROG', 'The Pragmatic Programmer', 'Your Journey to Mastery by David Thomas and Andrew Hunt.', 49.99, 39.99, 'simple', 3, 350, 'in_stock', TRUE),
    ('BK-DDD', 'Domain-Driven Design', 'Tackling Complexity in the Heart of Software by Eric Evans.', 59.99, NULL, 'simple', 3, 200, 'in_stock', TRUE);

-- CONFIGURABLE PRODUCT (will have variants)
INSERT INTO products (sku, name, description, price, special_price, product_type, attribute_set_id, stock_quantity, stock_status, is_active) VALUES
    ('TSH-PREMIUM', 'Premium Cotton T-Shirt', 'High-quality premium cotton t-shirt available in multiple sizes and colors.', 39.99, NULL, 'configurable', 1, 0, 'in_stock', TRUE);

-- SIMPLE VARIANTS for the configurable product
INSERT INTO products (sku, name, description, price, special_price, product_type, attribute_set_id, stock_quantity, stock_status, is_active) VALUES
    ('TSH-PREM-BLK-S', 'Premium Cotton T-Shirt - Black / S', 'Premium cotton t-shirt in Black, Size S', 39.99, NULL, 'simple', 1, 30, 'in_stock', TRUE),
    ('TSH-PREM-BLK-M', 'Premium Cotton T-Shirt - Black / M', 'Premium cotton t-shirt in Black, Size M', 39.99, NULL, 'simple', 1, 45, 'in_stock', TRUE),
    ('TSH-PREM-BLK-L', 'Premium Cotton T-Shirt - Black / L', 'Premium cotton t-shirt in Black, Size L', 39.99, NULL, 'simple', 1, 40, 'in_stock', TRUE),
    ('TSH-PREM-WHT-S', 'Premium Cotton T-Shirt - White / S', 'Premium cotton t-shirt in White, Size S', 39.99, NULL, 'simple', 1, 25, 'in_stock', TRUE),
    ('TSH-PREM-WHT-M', 'Premium Cotton T-Shirt - White / M', 'Premium cotton t-shirt in White, Size M', 39.99, NULL, 'simple', 1, 50, 'in_stock', TRUE),
    ('TSH-PREM-WHT-L', 'Premium Cotton T-Shirt - White / L', 'Premium cotton t-shirt in White, Size L', 39.99, NULL, 'simple', 1, 35, 'in_stock', TRUE);


-- =============================================
-- PRODUCT ATTRIBUTE VALUES (EAV data)
-- =============================================

-- Classic Black T-Shirt (id=1)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (1, 1, 'M'),      -- Size: M
    (1, 2, 'Black'),  -- Color: Black
    (1, 3, 'Cotton'); -- Material: Cotton

-- Classic White T-Shirt (id=2)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (2, 1, 'M'),      -- Size: M
    (2, 2, 'White'),  -- Color: White
    (2, 3, 'Cotton'); -- Material: Cotton

-- Slim Fit Blue Jeans (id=3)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (3, 1, 'L'),         -- Size: L (32 waist)
    (3, 2, 'Blue'),      -- Color: Blue
    (3, 3, 'Cotton');    -- Material: Cotton

-- Leather Jacket (id=4)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (4, 1, 'L'),      -- Size: L
    (4, 2, 'Black');  -- Color: Black

-- iPhone 15 128GB (id=5)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (5, 4, '128GB'),  -- Storage
    (5, 6, '6.1"'),   -- Screen Size
    (5, 7, 'Apple'),  -- Brand
    (5, 2, 'Black');  -- Color

-- iPhone 15 256GB (id=6)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (6, 4, '256GB'),  -- Storage
    (6, 6, '6.1"'),   -- Screen Size
    (6, 7, 'Apple'),  -- Brand
    (6, 2, 'Black');  -- Color

-- Samsung Galaxy S24 (id=7)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (7, 4, '128GB'),    -- Storage
    (7, 6, '6.1"'),     -- Screen Size
    (7, 7, 'Samsung'),  -- Brand
    (7, 2, 'Black');    -- Color

-- MacBook Pro 14" (id=8)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (8, 4, '512GB'),  -- Storage
    (8, 5, '16GB'),   -- RAM
    (8, 6, '14"'),    -- Screen Size
    (8, 7, 'Apple'),  -- Brand
    (8, 2, 'Gray');   -- Color

-- Dell XPS 15 (id=9)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (9, 4, '512GB'),   -- Storage
    (9, 5, '16GB'),    -- RAM
    (9, 6, '15.6"'),   -- Screen Size
    (9, 7, 'Dell'),    -- Brand
    (9, 2, 'Gray');    -- Color

-- Clean Code book (id=10)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (10, 8, 'Robert C. Martin'),        -- Author
    (10, 9, '978-0132350884'),           -- ISBN
    (10, 10, '464'),                     -- Pages
    (10, 11, 'Pearson');                 -- Publisher

-- The Pragmatic Programmer (id=11)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (11, 8, 'David Thomas, Andrew Hunt'), -- Author
    (11, 9, '978-0135957059'),             -- ISBN
    (11, 10, '352'),                       -- Pages
    (11, 11, 'Addison-Wesley');            -- Publisher

-- Domain-Driven Design (id=12)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (12, 8, 'Eric Evans'),        -- Author
    (12, 9, '978-0321125217'),    -- ISBN
    (12, 10, '560'),              -- Pages
    (12, 11, 'Addison-Wesley');   -- Publisher

-- Premium T-Shirt variants (id=14-19)
-- Black S (id=14)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (14, 1, 'S'), (14, 2, 'Black'), (14, 3, 'Cotton');
-- Black M (id=15)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (15, 1, 'M'), (15, 2, 'Black'), (15, 3, 'Cotton');
-- Black L (id=16)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (16, 1, 'L'), (16, 2, 'Black'), (16, 3, 'Cotton');
-- White S (id=17)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (17, 1, 'S'), (17, 2, 'White'), (17, 3, 'Cotton');
-- White M (id=18)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (18, 1, 'M'), (18, 2, 'White'), (18, 3, 'Cotton');
-- White L (id=19)
INSERT INTO product_attribute_values (product_id, attribute_id, value) VALUES
    (19, 1, 'L'), (19, 2, 'White'), (19, 3, 'Cotton');


-- =============================================
-- PRODUCT CATEGORIES (many-to-many)
-- =============================================

-- Clothing products to categories
INSERT INTO product_categories (product_id, category_id) VALUES
    (1, 1),   -- Classic Black T-Shirt → Men
    (1, 5),   -- Classic Black T-Shirt → Men's T-Shirts
    (2, 1),   -- Classic White T-Shirt → Men
    (2, 5),   -- Classic White T-Shirt → Men's T-Shirts
    (3, 1),   -- Slim Fit Blue Jeans → Men
    (3, 6),   -- Slim Fit Blue Jeans → Men's Jeans
    (4, 1),   -- Leather Jacket → Men
    (4, 7);   -- Leather Jacket → Men's Jackets

-- Electronics products to categories
INSERT INTO product_categories (product_id, category_id) VALUES
    (5, 3),   -- iPhone 15 128GB → Electronics
    (5, 11),  -- iPhone 15 128GB → Smartphones
    (6, 3),   -- iPhone 15 256GB → Electronics
    (6, 11),  -- iPhone 15 256GB → Smartphones
    (7, 3),   -- Samsung Galaxy S24 → Electronics
    (7, 11),  -- Samsung Galaxy S24 → Smartphones
    (8, 3),   -- MacBook Pro → Electronics
    (8, 12),  -- MacBook Pro → Laptops
    (9, 3),   -- Dell XPS → Electronics
    (9, 12);  -- Dell XPS → Laptops

-- Book products to categories
INSERT INTO product_categories (product_id, category_id) VALUES
    (10, 4),   -- Clean Code → Books
    (10, 16),  -- Clean Code → Technology
    (11, 4),   -- Pragmatic Programmer → Books
    (11, 16),  -- Pragmatic Programmer → Technology
    (12, 4),   -- DDD → Books
    (12, 16);  -- DDD → Technology

-- Premium T-Shirt (configurable) to categories
INSERT INTO product_categories (product_id, category_id) VALUES
    (13, 1),  -- Premium T-Shirt → Men
    (13, 5);  -- Premium T-Shirt → Men's T-Shirts


-- =============================================
-- CONFIGURABLE PRODUCT OPTIONS
-- Defines which attributes create variants
-- =============================================
INSERT INTO configurable_product_options (product_id, attribute_id) VALUES
    (13, 1),  -- Premium T-Shirt is configurable by Size
    (13, 2);  -- Premium T-Shirt is configurable by Color


-- =============================================
-- PRODUCT VARIANTS
-- Links configurable products to their variants
-- =============================================
INSERT INTO product_variants (parent_product_id, variant_product_id) VALUES
    (13, 14),  -- Premium T-Shirt → Black S
    (13, 15),  -- Premium T-Shirt → Black M
    (13, 16),  -- Premium T-Shirt → Black L
    (13, 17),  -- Premium T-Shirt → White S
    (13, 18),  -- Premium T-Shirt → White M
    (13, 19);  -- Premium T-Shirt → White L


-- =============================================
-- PRODUCT IMAGES
-- =============================================
INSERT INTO product_images (product_id, url, alt_text, position, is_primary) VALUES
    (1, '/images/products/tshirt-black-1.jpg', 'Classic Black T-Shirt Front', 1, TRUE),
    (1, '/images/products/tshirt-black-2.jpg', 'Classic Black T-Shirt Back', 2, FALSE),
    (2, '/images/products/tshirt-white-1.jpg', 'Classic White T-Shirt Front', 1, TRUE),
    (5, '/images/products/iphone-15-black-1.jpg', 'iPhone 15 Front View', 1, TRUE),
    (5, '/images/products/iphone-15-black-2.jpg', 'iPhone 15 Back View', 2, FALSE),
    (8, '/images/products/macbook-pro-14-1.jpg', 'MacBook Pro 14 Open', 1, TRUE),
    (10, '/images/products/clean-code-cover.jpg', 'Clean Code Book Cover', 1, TRUE),
    (11, '/images/products/pragmatic-programmer-cover.jpg', 'Pragmatic Programmer Cover', 1, TRUE);