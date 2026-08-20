<?php

/**
 * Database Seeder
 * Usage: php database/seed.php
 */

// Load environment variables if .env exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: 3306;
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'inventoryflow';

echo "========================================\n";
echo "  InventoryFlow - Database Seeder\n";
echo "========================================\n\n";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$database}";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $now = date('Y-m-d H:i:s');

    // ============================================
    // Seed Categories
    // ============================================
    echo "[1/5] Seeding categories...\n";

    $categories = [
        ['name' => 'Electronica', 'description' => 'Dispositivos electronicos y gadgets'],
        ['name' => 'Ropa', 'description' => 'Vestimenta y accesorios'],
        ['name' => 'Hogar', 'description' => 'Articulos para el hogar'],
        ['name' => 'Deportes', 'description' => 'Equipamiento deportivo'],
        ['name' => 'Alimentos', 'description' => 'Productos alimenticios'],
    ];

    $categoryIds = [];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, created_at) VALUES (?, ?, ?)");
        $stmt->execute([$cat['name'], $cat['description'], $now]);
        $categoryIds[] = (int) $pdo->lastInsertId();
    }
    echo "      " . count($categories) . " categories created.\n\n";

    // ============================================
    // Seed Suppliers
    // ============================================
    echo "[2/5] Seeding suppliers...\n";

    $suppliers = [
        ['name' => 'TechDistrib SA', 'contact' => 'Juan Perez', 'email' => 'juan@techdistrib.com', 'phone' => '555-0101', 'address' => 'Av. Tecnologia 123, CDMX'],
        ['name' => 'ModaExpress', 'contact' => 'Maria Lopez', 'email' => 'maria@modaexpress.com', 'phone' => '555-0102', 'address' => 'Calle Moda 456, Guadalajara'],
        ['name' => 'HogarTotal', 'contact' => 'Carlos Ruiz', 'email' => 'carlos@hogartotal.com', 'phone' => '555-0103', 'address' => 'Blvd. del Hogar 789, Monterrey'],
        ['name' => 'DeportesMax', 'contact' => 'Ana Garcia', 'email' => 'ana@deportesmax.com', 'phone' => '555-0104', 'address' => 'Av. Deportiva 321, Puebla'],
        ['name' => 'AlimentosFrescos', 'contact' => 'Roberto Diaz', 'email' => 'roberto@alfrescos.com', 'phone' => '555-0105', 'address' => 'Camino Rural 654, Leon'],
    ];

    $supplierIds = [];
    foreach ($suppliers as $sup) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact, email, phone, address, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, 'active', ?)");
        $stmt->execute([$sup['name'], $sup['contact'], $sup['email'], $sup['phone'], $sup['address'], $now]);
        $supplierIds[] = (int) $pdo->lastInsertId();
    }
    echo "      " . count($suppliers) . " suppliers created.\n\n";

    // ============================================
    // Seed Products
    // ============================================
    echo "[3/5] Seeding products...\n";

    $products = [
        // Electronica
        ['name' => 'Laptop HP Pavilion 15', 'sku' => 'ELEC-001', 'description' => 'Laptop 15 pulgadas, 16GB RAM, 512GB SSD', 'price' => 18999.99, 'cost' => 14500.00, 'stock' => 25, 'min_stock' => 5, 'cat_idx' => 0, 'sup_idx' => 0],
        ['name' => 'Mouse Logitech MX Master 3', 'sku' => 'ELEC-002', 'description' => 'Mouse inalambrico ergonomico', 'price' => 1299.99, 'cost' => 850.00, 'stock' => 50, 'min_stock' => 10, 'cat_idx' => 0, 'sup_idx' => 0],
        ['name' => 'Teclado Mecanico RGB', 'sku' => 'ELEC-003', 'description' => 'Teclado mecanico switches Cherry MX', 'price' => 1599.99, 'cost' => 1000.00, 'stock' => 30, 'min_stock' => 8, 'cat_idx' => 0, 'sup_idx' => 0],
        ['name' => 'Monitor Samsung 27"', 'sku' => 'ELEC-004', 'description' => 'Monitor 4K UHD, 60Hz', 'price' => 7499.99, 'cost' => 5200.00, 'stock' => 15, 'min_stock' => 3, 'cat_idx' => 0, 'sup_idx' => 0],
        ['name' => 'Audifonos Sony WH-1000XM5', 'sku' => 'ELEC-005', 'description' => 'Audifonos con cancelacion de ruido', 'price' => 5999.99, 'cost' => 3800.00, 'stock' => 20, 'min_stock' => 5, 'cat_idx' => 0, 'sup_idx' => 0],

        // Ropa
        ['name' => 'Camiseta Basica Algodon', 'sku' => 'ROPA-001', 'description' => 'Camiseta 100% algodon, varios colores', 'price' => 249.99, 'cost' => 120.00, 'stock' => 200, 'min_stock' => 30, 'cat_idx' => 1, 'sup_idx' => 1],
        ['name' => 'Jeans Clasicos', 'sku' => 'ROPA-002', 'description' => 'Jeans straight fit, denim premium', 'price' => 699.99, 'cost' => 350.00, 'stock' => 100, 'min_stock' => 20, 'cat_idx' => 1, 'sup_idx' => 1],
        ['name' => 'Chamarra de Mezclilla', 'sku' => 'ROPA-003', 'description' => 'Chamarra casual denim', 'price' => 1299.99, 'cost' => 700.00, 'stock' => 45, 'min_stock' => 10, 'cat_idx' => 1, 'sup_idx' => 1],

        // Hogar
        ['name' => 'Licuadora Oster 10 vel', 'sku' => 'HOGA-001', 'description' => 'Licuadora de vaso, 10 velocidades', 'price' => 899.99, 'cost' => 550.00, 'stock' => 35, 'min_stock' => 8, 'cat_idx' => 2, 'sup_idx' => 2],
        ['name' => 'Sarten Antiadherente 28cm', 'sku' => 'HOGA-002', 'description' => 'Sarten con recubrimiento ceramico', 'price' => 449.99, 'cost' => 220.00, 'stock' => 60, 'min_stock' => 15, 'cat_idx' => 2, 'sup_idx' => 2],
        ['name' => 'Aspiradora Robot Xiaomi', 'sku' => 'HOGA-003', 'description' => 'Aspiradora robot con mapeo laser', 'price' => 4999.99, 'cost' => 3200.00, 'stock' => 10, 'min_stock' => 3, 'cat_idx' => 2, 'sup_idx' => 2],

        // Deportes
        ['name' => 'Bicicleta de Monta a 21v', 'sku' => 'DEPO-001', 'description' => 'Bicicleta MTB 21 velocidades', 'price' => 3999.99, 'cost' => 2500.00, 'stock' => 12, 'min_stock' => 3, 'cat_idx' => 3, 'sup_idx' => 3],
        ['name' => 'Mancuernas Set 20kg', 'sku' => 'DEPO-002', 'description' => 'Set de mancuernas ajustables', 'price' => 1599.99, 'cost' => 900.00, 'stock' => 25, 'min_stock' => 5, 'cat_idx' => 3, 'sup_idx' => 3],
        ['name' => 'Pelota de Futbol Adidas', 'sku' => 'DEPO-003', 'description' => 'Pelota oficial tamaño 5', 'price' => 599.99, 'cost' => 300.00, 'stock' => 80, 'min_stock' => 20, 'cat_idx' => 3, 'sup_idx' => 3],

        // Alimentos
        ['name' => 'Cafe Orgánico 500g', 'sku' => 'ALIM-001', 'description' => 'Cafe de origen Chiapas', 'price' => 189.99, 'cost' => 100.00, 'stock' => 150, 'min_stock' => 30, 'cat_idx' => 4, 'sup_idx' => 4],
        ['name' => 'Aceite de Oliva Extra Virgen 1L', 'sku' => 'ALIM-002', 'description' => 'Aceite de oliva importado', 'price' => 299.99, 'cost' => 180.00, 'stock' => 80, 'min_stock' => 20, 'cat_idx' => 4, 'sup_idx' => 4],
        ['name' => 'Chocolate Artesano 200g', 'sku' => 'ALIM-003', 'description' => 'Chocolate de fina aroma', 'price' => 149.99, 'cost' => 75.00, 'stock' => 100, 'min_stock' => 25, 'cat_idx' => 4, 'sup_idx' => 4],
    ];

    $productIds = [];
    foreach ($products as $prod) {
        $stmt = $pdo->prepare("INSERT INTO products (name, sku, description, price, cost, stock, min_stock, category_id, supplier_id, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)");
        $stmt->execute([
            $prod['name'], $prod['sku'], $prod['description'],
            $prod['price'], $prod['cost'], $prod['stock'], $prod['min_stock'],
            $categoryIds[$prod['cat_idx']], $supplierIds[$prod['sup_idx']],
            $now,
        ]);
        $productIds[] = (int) $pdo->lastInsertId();
    }
    echo "      " . count($products) . " products created.\n\n";

    // ============================================
    // Seed Customers
    // ============================================
    echo "[4/5] Seeding customers...\n";

    $customers = [
        ['name' => 'Pedro Ramirez', 'email' => 'pedro.ramirez@email.com', 'phone' => '555-1001', 'address' => 'Calle Norte 100', 'rfc' => 'RAPR850101ABC'],
        ['name' => 'Laura Sanchez', 'email' => 'laura.sanchez@email.com', 'phone' => '555-1002', 'address' => 'Av. Reforma 200', 'rfc' => 'SAML900215DEF'],
        ['name' => 'Miguel Torres', 'email' => 'miguel.torres@email.com', 'phone' => '555-1003', 'address' => 'Blvd. Insurgentes 300', 'rfc' => 'TOMM780320GHI'],
        ['name' => 'Sofia Hernandez', 'email' => 'sofia.hernandez@email.com', 'phone' => '555-1004', 'address' => 'Calle Sur 400', 'rfc' => 'HESO920410JKL'],
        ['name' => 'Fernando Cruz', 'email' => 'fernando.cruz@email.com', 'phone' => '555-1005', 'address' => 'Av. Juarez 500', 'rfc' => 'CUFJ870525MNO'],
    ];

    $customerIds = [];
    foreach ($customers as $cust) {
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address, rfc, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cust['name'], $cust['email'], $cust['phone'], $cust['address'], $cust['rfc'], $now]);
        $customerIds[] = (int) $pdo->lastInsertId();
    }
    echo "      " . count($customers) . " customers created.\n\n";

    // ============================================
    // Seed Sales
    // ============================================
    echo "[5/5] Seeding sales...\n";

    $taxRate = 0.16;
    $saleCount = 0;

    for ($i = 0; $i < 20; $i++) {
        $customerId = $customerIds[array_rand($customerIds)];
        $userId = 1;
        $numItems = rand(1, 4);

        $subtotal = 0;
        $items = [];

        for ($j = 0; $j < $numItems; $j++) {
            $productId = $productIds[array_rand($productIds)];
            $quantity = rand(1, 3);

            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $price = (float) $stmt->fetchColumn();

            $itemSubtotal = $price * $quantity;
            $subtotal += $itemSubtotal;

            $items[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $price,
                'subtotal'   => $itemSubtotal,
            ];
        }

        $discount = ($i % 5 === 0) ? $subtotal * 0.10 : 0;
        $taxableAmount = $subtotal - $discount;
        $tax = $taxableAmount * $taxRate;
        $total = $taxableAmount + $tax;

        // Random date in last 30 days
        $daysAgo = rand(0, 30);
        $saleDate = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

        // Create sale
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, user_id, subtotal, discount, tax, total, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 'completed', ?)");
        $stmt->execute([$customerId, $userId, $subtotal, $discount, $tax, $total, $saleDate]);
        $saleId = (int) $pdo->lastInsertId();

        // Create sale items
        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$saleId, $item['product_id'], $item['quantity'], $item['price'], $item['subtotal']]);

            // Decrease stock
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        $saleCount++;
    }

    echo "      {$saleCount} sales created.\n\n";

    echo "========================================\n";
    echo "  Seeding completed successfully!\n";
    echo "========================================\n\n";
    echo "Test credentials:\n";
    echo "  Email: admin@inventoryflow.com\n";
    echo "  Password: Admin123!\n\n";

} catch (PDOException $e) {
    echo "\nError: " . $e->getMessage() . "\n\n";
    echo "Make sure you have run migrate.php first.\n";
    exit(1);
}
