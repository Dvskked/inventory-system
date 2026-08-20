<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Supplier;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;

/**
 * Supplier Controller
 */
class SupplierController extends Controller
{
    private Supplier $supplierModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->supplierModel = new Supplier();
    }

    /**
     * List suppliers
     */
    public function index(): void
    {
        $suppliers = $this->supplierModel->getAllWithCount();

        $this->view('suppliers.index', [
            'title'     => 'Proveedores',
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('suppliers.form', [
            'title'    => 'Nuevo Proveedor',
            'supplier' => null,
        ]);
    }

    /**
     * Store new supplier
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/suppliers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/suppliers/create');
        }

        $validator = Validator::make($_POST);
        $validator->required(['name', 'contact'])
                  ->email(['email'])
                  ->phone(['phone'])
                  ->minLength(['name' => 3, 'contact' => 3])
                  ->maxLength(['name' => 200, 'address' => 500]);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/suppliers/create');
        }

        $supplierId = $this->supplierModel->create([
            'name'       => $this->sanitize($_POST['name']),
            'contact'    => $this->sanitize($_POST['contact']),
            'email'      => $this->sanitize($_POST['email'] ?? ''),
            'phone'      => $this->sanitize($_POST['phone'] ?? ''),
            'address'    => $this->sanitize($_POST['address'] ?? ''),
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Proveedor creado exitosamente');
        $this->redirect('/suppliers');
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $supplier = $this->supplierModel->find((int) $id);

        if (!$supplier) {
            $this->setFlash('error', 'Proveedor no encontrado');
            $this->redirect('/suppliers');
        }

        $this->view('suppliers.form', [
            'title'    => 'Editar Proveedor',
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update supplier
     */
    public function update(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/suppliers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/suppliers/edit/' . $id);
        }

        $validator = Validator::make($_POST);
        $validator->required(['name', 'contact'])
                  ->email(['email'])
                  ->phone(['phone']);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/suppliers/edit/' . $id);
        }

        $this->supplierModel->update((int) $id, [
            'name'    => $this->sanitize($_POST['name']),
            'contact' => $this->sanitize($_POST['contact']),
            'email'   => $this->sanitize($_POST['email'] ?? ''),
            'phone'   => $this->sanitize($_POST['phone'] ?? ''),
            'address' => $this->sanitize($_POST['address'] ?? ''),
            'status'  => $_POST['status'] ?? 'active',
        ]);

        $this->setFlash('success', 'Proveedor actualizado exitosamente');
        $this->redirect('/suppliers');
    }

    /**
     * Delete supplier
     */
    public function delete(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/suppliers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/suppliers');
        }

        try {
            $this->supplierModel->safeDelete((int) $id);
            $this->setFlash('success', 'Proveedor eliminado exitosamente');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/suppliers');
    }
}
