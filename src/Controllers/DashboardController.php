<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Product;
use InventoryFlow\Models\Sale;
use InventoryFlow\Models\Customer;
use InventoryFlow\Models\Category;

/**
 * Dashboard Controller
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Show dashboard
     */
    public function index(): void
    {
        $productModel = new Product();
        $saleModel = new Sale();
        $customerModel = new Customer();
        $categoryModel = new Category();

        // Get statistics
        $productStats = $productModel->getStats();
        $saleStats = $saleModel->getStats();
        $customerStats = $customerModel->getStats();

        // Get recent data
        $recentSales = $saleModel->getRecent(5);
        $lowStockProducts = $productModel->getLowStock();
        $topSelling = $productModel->getTopSelling(5);
        $topCustomers = $customerModel->getTopCustomers(5);

        // Daily sales for chart (last 30 days)
        $dailySales = $saleModel->getDailySummary(30);

        // Category distribution
        $categories = $categoryModel->getAllWithCount();

        $this->view('dashboard.index', [
            'title'              => 'Dashboard',
            'productStats'       => $productStats,
            'saleStats'          => $saleStats,
            'customerStats'      => $customerStats,
            'recentSales'        => $recentSales,
            'lowStockProducts'   => $lowStockProducts,
            'topSelling'         => $topSelling,
            'topCustomers'       => $topCustomers,
            'dailySales'         => $dailySales,
            'categories'         => $categories,
        ]);
    }
}
