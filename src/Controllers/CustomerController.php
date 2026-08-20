<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Customer;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;

/**
 * Customer Controller
 */
class CustomerController extends Controller
{
    private Customer $customerModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->customerModel = new Customer();
    }

    /**
     * List customers
     */
    public function index(): void
    {
        $search = $this->input('search', '');

        if ($search !== '') {
            $customers = $this->customerModel->searchCustomers($search);
        } else {
            $customers = $this->customerModel->getAllWithStats();
        }

        $this->view('customers.index', [
            'title'     => 'Clientes',
            'customers' => $customers,
            'search'    => $search,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('customers.form', [
            'title'    => 'Nuevo Cliente',
            'customer' => null,
        ]);
    }

    /**
     * Store new customer
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/customers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/customers/create');
        }

        $validator = Validator::make($_POST);
        $validator->required(['name'])
                  ->email(['email'])
                  ->phone(['phone'])
                  ->minLength(['name' => 3])
                  ->maxLength(['name' => 200, 'address' => 500]);

        if (!empty($_POST['rfc'])) {
            $validator->rfc(['rfc']);
        }

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/customers/create');
        }

        // Check unique email if provided
        if (!empty($_POST['email'])) {
            $existing = $this->customerModel->findByEmail($_POST['email']);
            if ($existing) {
                $this->setFlash('error', 'El correo ya esta registrado');
                $this->redirect('/customers/create');
            }
        }

        // Check unique RFC if provided
        if (!empty($_POST['rfc'])) {
            $existing = $this->customerModel->findByRfc($_POST['rfc']);
            if ($existing) {
                $this->setFlash('error', 'El RFC ya esta registrado');
                $this->redirect('/customers/create');
            }
        }

        $customerId = $this->customerModel->create([
            'name'       => $this->sanitize($_POST['name']),
            'email'      => $this->sanitize($_POST['email'] ?? ''),
            'phone'      => $this->sanitize($_POST['phone'] ?? ''),
            'address'    => $this->sanitize($_POST['address'] ?? ''),
            'rfc'        => strtoupper($this->sanitize($_POST['rfc'] ?? '')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Cliente creado exitosamente');
        $this->redirect('/customers');
    }

    /**
     * Show customer details
     */
    public function show(string $id): void
    {
        $customer = $this->customerModel->getWithStats((int) $id);

        if (!$customer) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect('/customers');
        }

        $saleModel = new \InventoryFlow\Models\Sale();
        $purchases = $saleModel->all(
            ['customer_id' => $id, 'status' => 'completed'],
            'created_at DESC'
        );

        $this->view('customers.show', [
            'title'     => $customer['name'],
            'customer'  => $customer,
            'purchases' => $purchases,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $customer = $this->customerModel->find((int) $id);

        if (!$customer) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect('/customers');
        }

        $this->view('customers.form', [
            'title'    => 'Editar Cliente',
            'customer' => $customer,
        ]);
    }

    /**
     * Update customer
     */
    public function update(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/customers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/customers/edit/' . $id);
        }

        $validator = Validator::make($_POST);
        $validator->required(['name'])
                  ->email(['email'])
                  ->phone(['phone']);

        if (!empty($_POST['rfc'])) {
            $validator->rfc(['rfc']);
        }

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/customers/edit/' . $id);
        }

        $this->customerModel->update((int) $id, [
            'name'    => $this->sanitize($_POST['name']),
            'email'   => $this->sanitize($_POST['email'] ?? ''),
            'phone'   => $this->sanitize($_POST['phone'] ?? ''),
            'address' => $this->sanitize($_POST['address'] ?? ''),
            'rfc'     => strtoupper($this->sanitize($_POST['rfc'] ?? '')),
        ]);

        $this->setFlash('success', 'Cliente actualizado exitosamente');
        $this->redirect('/customers');
    }

    /**
     * Delete customer
     */
    public function delete(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/customers');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/customers');
        }

        $this->customerModel->delete((int) $id);
        $this->setFlash('success', 'Cliente eliminado exitosamente');
        $this->redirect('/customers');
    }
}
