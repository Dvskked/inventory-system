<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Product;
use InventoryFlow\Models\Category;
use InventoryFlow\Models\Supplier;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;
use InventoryFlow\Helpers\Pagination;

/**
 * Product Controller
 */
class ProductController extends Controller
{
    private Product $productModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->productModel = new Product();
    }

    /**
     * List products
     */
    public function index(): void
    {
        $search = $this->input('search', '');
        $categoryId = (int) $this->input('category', 0);
        $page = max(1, (int) $this->input('page', 1));
        $perPage = 15;

        $result = $this->productModel->getPaginated($page, $perPage, $search, $categoryId);
        $pagination = new Pagination($result['total'], $result['page'], $result['per_page']);

        $categoryModel = new Category();
        $categories = $categoryModel->all([], 'name ASC');

        $this->view('products.index', [
            'title'      => 'Productos',
            'products'   => $result['data'],
            'pagination' => $pagination,
            'search'     => $search,
            'categoryId' => $categoryId,
            'categories' => $categories,
            'total'      => $result['total'],
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $categoryModel = new Category();
        $supplierModel = new Supplier();

        $this->view('products.create', [
            'title'     => 'Nuevo Producto',
            'categories' => $categoryModel->all([], 'name ASC'),
            'suppliers'  => $supplierModel->getActive(),
        ]);
    }

    /**
     * Store new product
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/products');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/products/create');
        }

        $validator = Validator::make($_POST);
        $validator->required(['name', 'sku', 'price', 'category_id'])
                  ->sku(['sku'])
                  ->positive(['price', 'cost'])
                  ->minLength(['name' => 3, 'sku' => 3])
                  ->maxLength(['name' => 200, 'sku' => 50]);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/products/create');
        }

        // Check unique SKU
        if ($this->productModel->skuExists($_POST['sku'])) {
            $this->setFlash('error', 'El SKU ya existe en el sistema');
            $this->redirect('/products/create');
        }

        $productId = $this->productModel->create([
            'name'        => $this->sanitize($_POST['name']),
            'sku'         => strtoupper($this->sanitize($_POST['sku'])),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'price'       => (float) $_POST['price'],
            'cost'        => (float) ($_POST['cost'] ?? 0),
            'stock'       => (int) ($_POST['stock'] ?? 0),
            'min_stock'   => (int) ($_POST['min_stock'] ?? 5),
            'category_id' => (int) $_POST['category_id'],
            'supplier_id' => !empty($_POST['supplier_id']) ? (int) $_POST['supplier_id'] : null,
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Producto creado exitosamente');
        $this->redirect('/products/' . $productId);
    }

    /**
     * Show product details
     */
    public function show(string $id): void
    {
        $product = $this->productModel->getWithRelations((int) $id);

        if (!$product) {
            $this->setFlash('error', 'Producto no encontrado');
            $this->redirect('/products');
        }

        $this->view('products.show', [
            'title'   => $product['name'],
            'product' => $product,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $product = $this->productModel->find((int) $id);

        if (!$product) {
            $this->setFlash('error', 'Producto no encontrado');
            $this->redirect('/products');
        }

        $categoryModel = new Category();
        $supplierModel = new Supplier();

        $this->view('products.edit', [
            'title'      => 'Editar Producto',
            'product'    => $product,
            'categories' => $categoryModel->all([], 'name ASC'),
            'suppliers'  => $supplierModel->getActive(),
        ]);
    }

    /**
     * Update product
     */
    public function update(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/products');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/products/edit/' . $id);
        }

        $validator = Validator::make($_POST);
        $validator->required(['name', 'sku', 'price', 'category_id'])
                  ->sku(['sku'])
                  ->positive(['price', 'cost']);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/products/edit/' . $id);
        }

        // Check unique SKU (excluding current product)
        if ($this->productModel->skuExists($_POST['sku'], (int) $id)) {
            $this->setFlash('error', 'El SKU ya existe en el sistema');
            $this->redirect('/products/edit/' . $id);
        }

        $this->productModel->update((int) $id, [
            'name'        => $this->sanitize($_POST['name']),
            'sku'         => strtoupper($this->sanitize($_POST['sku'])),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'price'       => (float) $_POST['price'],
            'cost'        => (float) ($_POST['cost'] ?? 0),
            'min_stock'   => (int) ($_POST['min_stock'] ?? 5),
            'category_id' => (int) $_POST['category_id'],
            'supplier_id' => !empty($_POST['supplier_id']) ? (int) $_POST['supplier_id'] : null,
            'status'      => $_POST['status'] ?? 'active',
        ]);

        $this->setFlash('success', 'Producto actualizado exitosamente');
        $this->redirect('/products/' . $id);
    }

    /**
     * Delete product
     */
    public function delete(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/products');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/products');
        }

        try {
            $this->productModel->delete((int) $id);
            $this->setFlash('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/products');
    }

    /**
     * Search products (AJAX)
     */
    public function search(): void
    {
        $term = $this->input('q', '');

        if (strlen($term) < 2) {
            $this->json([]);
        }

        $results = $this->productModel->searchProducts($term);

        $this->json(array_map(fn($p) => [
            'id'    => $p['id'],
            'name'  => $p['name'],
            'sku'   => $p['sku'],
            'price' => $p['price'],
            'stock' => $p['stock'],
        ], array_slice($results, 0, 10)));
    }
}
