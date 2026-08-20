<?php

/**
 * Web Routes
 */

use InventoryFlow\Core\Router;

$router = new Router();

// ==========================================
// Authentication Routes
// ==========================================
$router->get('/login', [\InventoryFlow\Controllers\AuthController::class, 'loginForm']);
$router->post('/login', [\InventoryFlow\Controllers\AuthController::class, 'login']);
$router->get('/register', [\InventoryFlow\Controllers\AuthController::class, 'registerForm']);
$router->post('/register', [\InventoryFlow\Controllers\AuthController::class, 'register']);
$router->get('/logout', [\InventoryFlow\Controllers\AuthController::class, 'logout']);

// ==========================================
// Dashboard
// ==========================================
$router->get('/dashboard', [\InventoryFlow\Controllers\DashboardController::class, 'index']);

// ==========================================
// Products
// ==========================================
$router->get('/products', [\InventoryFlow\Controllers\ProductController::class, 'index']);
$router->get('/products/create', [\InventoryFlow\Controllers\ProductController::class, 'create']);
$router->post('/products/store', [\InventoryFlow\Controllers\ProductController::class, 'store']);
$router->get('/products/{id}', [\InventoryFlow\Controllers\ProductController::class, 'show']);
$router->get('/products/edit/{id}', [\InventoryFlow\Controllers\ProductController::class, 'edit']);
$router->post('/products/update/{id}', [\InventoryFlow\Controllers\ProductController::class, 'update']);
$router->post('/products/delete/{id}', [\InventoryFlow\Controllers\ProductController::class, 'delete']);
$router->get('/api/products/search', [\InventoryFlow\Controllers\ProductController::class, 'search']);

// ==========================================
// Categories
// ==========================================
$router->get('/categories', [\InventoryFlow\Controllers\CategoryController::class, 'index']);
$router->get('/categories/create', [\InventoryFlow\Controllers\CategoryController::class, 'create']);
$router->post('/categories/store', [\InventoryFlow\Controllers\CategoryController::class, 'store']);
$router->get('/categories/edit/{id}', [\InventoryFlow\Controllers\CategoryController::class, 'edit']);
$router->post('/categories/update/{id}', [\InventoryFlow\Controllers\CategoryController::class, 'update']);
$router->post('/categories/delete/{id}', [\InventoryFlow\Controllers\CategoryController::class, 'delete']);

// ==========================================
// Suppliers
// ==========================================
$router->get('/suppliers', [\InventoryFlow\Controllers\SupplierController::class, 'index']);
$router->get('/suppliers/create', [\InventoryFlow\Controllers\SupplierController::class, 'create']);
$router->post('/suppliers/store', [\InventoryFlow\Controllers\SupplierController::class, 'store']);
$router->get('/suppliers/edit/{id}', [\InventoryFlow\Controllers\SupplierController::class, 'edit']);
$router->post('/suppliers/update/{id}', [\InventoryFlow\Controllers\SupplierController::class, 'update']);
$router->post('/suppliers/delete/{id}', [\InventoryFlow\Controllers\SupplierController::class, 'delete']);

// ==========================================
// Customers
// ==========================================
$router->get('/customers', [\InventoryFlow\Controllers\CustomerController::class, 'index']);
$router->get('/customers/create', [\InventoryFlow\Controllers\CustomerController::class, 'create']);
$router->post('/customers/store', [\InventoryFlow\Controllers\CustomerController::class, 'store']);
$router->get('/customers/{id}', [\InventoryFlow\Controllers\CustomerController::class, 'show']);
$router->get('/customers/edit/{id}', [\InventoryFlow\Controllers\CustomerController::class, 'edit']);
$router->post('/customers/update/{id}', [\InventoryFlow\Controllers\CustomerController::class, 'update']);
$router->post('/customers/delete/{id}', [\InventoryFlow\Controllers\CustomerController::class, 'delete']);

// ==========================================
// Sales
// ==========================================
$router->get('/sales', [\InventoryFlow\Controllers\SaleController::class, 'index']);
$router->get('/sales/create', [\InventoryFlow\Controllers\SaleController::class, 'create']);
$router->post('/sales/store', [\InventoryFlow\Controllers\SaleController::class, 'store']);
$router->get('/sales/{id}', [\InventoryFlow\Controllers\SaleController::class, 'show']);
$router->post('/sales/cancel/{id}', [\InventoryFlow\Controllers\SaleController::class, 'cancel']);
$router->get('/api/sales/product', [\InventoryFlow\Controllers\SaleController::class, 'getProduct']);
$router->get('/api/sales/chart', [\InventoryFlow\Controllers\SaleController::class, 'getChartData']);

// ==========================================
// Reports
// ==========================================
$router->get('/reports', [\InventoryFlow\Controllers\ReportController::class, 'index']);
$router->get('/reports/sales', [\InventoryFlow\Controllers\ReportController::class, 'sales']);
$router->get('/reports/inventory', [\InventoryFlow\Controllers\ReportController::class, 'inventory']);
$router->get('/reports/customers', [\InventoryFlow\Controllers\ReportController::class, 'customers']);
$router->get('/reports/export', [\InventoryFlow\Controllers\ReportController::class, 'export']);

// ==========================================
// Home redirect
// ==========================================
$router->get('/', [\InventoryFlow\Controllers\DashboardController::class, 'index']);

// Dispatch
$router->dispatch();
