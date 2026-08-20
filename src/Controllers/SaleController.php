<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Sale;
use InventoryFlow\Models\Product;
use InventoryFlow\Models\Customer;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;

/**
 * Sale Controller
 */
class SaleController extends Controller
{
    private Sale $saleModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->saleModel = new Sale();
    }

    /**
     * List sales
     */
    public function index(): void
    {
        $search = $this->input('search', '');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');
        $page = max(1, (int) $this->input('page', 1));
        $perPage = 20;

        if ($startDate !== '' && $endDate !== '') {
            $sales = $this->saleModel->getByDateRange($startDate, $endDate);
        } else {
            $offset = ($page - 1) * $perPage;
            $sales = $this->saleModel->getAllWithRelations($perPage, $offset);
        }

        $stats = $this->saleModel->getStats($startDate, $endDate);

        $this->view('sales.index', [
            'title'     => 'Ventas',
            'sales'     => $sales,
            'stats'     => $stats,
            'search'    => $search,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    /**
     * Show create sale form (POS)
     */
    public function create(): void
    {
        $productModel = new Product();
        $customerModel = new Customer();

        $this->view('sales.create', [
            'title'     => 'Nueva Venta',
            'products'  => $productModel->all(['status' => 'active'], 'name ASC'),
            'customers' => $customerModel->all([], 'name ASC'),
            'ticket'    => $this->saleModel->generateTicketNumber(),
        ]);
    }

    /**
     * Process new sale
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/sales');
        }

        if (!CSRF::verify()) {
            $this->json(['success' => false, 'message' => 'Token invalido'], 403);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['items'])) {
            $this->json(['success' => false, 'message' => 'No hay items en la venta'], 400);
        }

        $validator = Validator::make($data);
        $validator->positive(['total']);

        if ($validator->fails()) {
            $this->json(['success' => false, 'message' => $validator->getFirstError()], 400);
        }

        try {
            $customerId = !empty($data['customer_id']) ? (int) $data['customer_id'] : null;
            $discount = (float) ($data['discount'] ?? 0);
            $subtotal = (float) $data['subtotal'];
            $tax = (float) $data['tax'];
            $total = (float) $data['total'];

            $saleId = $this->saleModel->createSale(
                [
                    'customer_id' => $customerId,
                    'user_id'     => $this->userId(),
                    'subtotal'    => $subtotal,
                    'discount'    => $discount,
                    'tax'         => $tax,
                    'total'       => $total,
                    'status'      => 'completed',
                    'notes'       => $data['notes'] ?? '',
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
                $data['items']
            );

            $this->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente',
                'sale_id' => $saleId,
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show sale details
     */
    public function show(string $id): void
    {
        $sale = $this->saleModel->getWithDetails((int) $id);

        if (!$sale) {
            $this->setFlash('error', 'Venta no encontrada');
            $this->redirect('/sales');
        }

        $this->view('sales.show', [
            'title' => 'Detalle de Venta #' . $sale['id'],
            'sale'  => $sale,
        ]);
    }

    /**
     * Cancel a sale
     */
    public function cancel(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/sales');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/sales');
        }

        try {
            $this->saleModel->cancelSale((int) $id);
            $this->setFlash('success', 'Venta cancelada y stock restaurado');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/sales');
    }

    /**
     * Get product data for POS (AJAX)
     */
    public function getProduct(): void
    {
        $productId = (int) $this->input('id', 0);

        if ($productId <= 0) {
            $this->json(null, 404);
        }

        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            $this->json(null, 404);
        }

        $this->json([
            'id'       => $product['id'],
            'name'     => $product['name'],
            'sku'      => $product['sku'],
            'price'    => $product['price'],
            'stock'    => $product['stock'],
        ]);
    }

    /**
     * Get daily sales data for charts (AJAX)
     */
    public function getChartData(): void
    {
        $days = (int) $this->input('days', 30);
        $data = $this->saleModel->getDailySummary($days);

        $this->json($data);
    }
}
