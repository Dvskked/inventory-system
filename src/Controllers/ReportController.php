<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Product;
use InventoryFlow\Models\Sale;
use InventoryFlow\Models\Customer;
use InventoryFlow\Models\Category;

/**
 * Report Controller
 */
class ReportController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Show reports index
     */
    public function index(): void
    {
        $productModel = new Product();
        $saleModel = new Sale();
        $customerModel = new Customer();
        $categoryModel = new Category();

        $productStats = $productModel->getStats();
        $saleStats = $saleModel->getStats();
        $customerStats = $customerModel->getStats();
        $categoryStats = $categoryModel->getStats();

        $this->view('reports.index', [
            'title'          => 'Reportes',
            'productStats'   => $productStats,
            'saleStats'      => $saleStats,
            'customerStats'  => $customerStats,
            'categoryStats'  => $categoryStats,
        ]);
    }

    /**
     * Sales report
     */
    public function sales(): void
    {
        $startDate = $this->input('start_date', date('Y-m-01'));
        $endDate = $this->input('end_date', date('Y-m-t'));

        $saleModel = new Sale();

        $sales = $saleModel->getByDateRange($startDate, $endDate);
        $stats = $saleModel->getStats($startDate, $endDate);
        $dailySummary = $saleModel->getDailySummary(60);
        $monthlySummary = $saleModel->getMonthlySummary(12);

        $this->view('reports.sales', [
            'title'           => 'Reporte de Ventas',
            'sales'           => $sales,
            'stats'           => $stats,
            'dailySummary'    => $dailySummary,
            'monthlySummary'  => $monthlySummary,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
        ]);
    }

    /**
     * Inventory report
     */
    public function inventory(): void
    {
        $productModel = new Product();

        $products = $productModel->getAllWithRelations(500);
        $lowStock = $productModel->getLowStock();
        $outOfStock = $productModel->getOutOfStock();
        $topSelling = $productModel->getTopSelling(20);
        $stats = $productModel->getStats();

        $this->view('reports.inventory', [
            'title'       => 'Reporte de Inventario',
            'products'    => $products,
            'lowStock'    => $lowStock,
            'outOfStock'  => $outOfStock,
            'topSelling'  => $topSelling,
            'stats'       => $stats,
        ]);
    }

    /**
     * Customer report
     */
    public function customers(): void
    {
        $customerModel = new Customer();

        $customers = $customerModel->getAllWithStats();
        $topCustomers = $customerModel->getTopCustomers(20);
        $stats = $customerModel->getStats();

        $this->view('reports.customers', [
            'title'         => 'Reporte de Clientes',
            'customers'     => $customers,
            'topCustomers'  => $topCustomers,
            'stats'         => $stats,
        ]);
    }

    /**
     * Export data as CSV
     */
    public function export(): void
    {
        $type = $this->input('type', '');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $type . '_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'products':
                $this->exportProducts($output);
                break;
            case 'sales':
                $this->exportSales($output);
                break;
            case 'customers':
                $this->exportCustomers($output);
                break;
            default:
                fclose($output);
                $this->redirect('/reports');
        }

        fclose($output);
        exit;
    }

    private function exportProducts($output): void
    {
        fputcsv($output, ['ID', 'Nombre', 'SKU', 'Precio', 'Costo', 'Stock', 'Categoria', 'Proveedor', 'Estado']);

        $model = new Product();
        $products = $model->getAllWithRelations(1000);

        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['sku'],
                $product['price'],
                $product['cost'],
                $product['stock'],
                $product['category_name'] ?? '',
                $product['supplier_name'] ?? '',
                $product['status'],
            ]);
        }
    }

    private function exportSales($output): void
    {
        fputcsv($output, ['ID', 'Fecha', 'Cliente', 'Vendedor', 'Subtotal', 'Descuento', 'IVA', 'Total', 'Estado']);

        $model = new Sale();
        $sales = $model->getAllWithRelations(1000);

        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale['id'],
                $sale['created_at'],
                $sale['customer_name'] ?? 'Publico General',
                $sale['user_name'],
                $sale['subtotal'],
                $sale['discount'],
                $sale['tax'],
                $sale['total'],
                $sale['status'],
            ]);
        }
    }

    private function exportCustomers($output): void
    {
        fputcsv($output, ['ID', 'Nombre', 'Email', 'Telefono', 'RFC', 'Compras', 'Total Gastado']);

        $model = new Customer();
        $customers = $model->getAllWithStats();

        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['id'],
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $customer['rfc'] ?? '',
                $customer['total_purchases'],
                $customer['total_spent'],
            ]);
        }
    }
}
