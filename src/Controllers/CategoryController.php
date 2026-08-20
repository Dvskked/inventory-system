<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Models\Category;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;

/**
 * Category Controller
 */
class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->categoryModel = new Category();
    }

    /**
     * List categories
     */
    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithCount();

        $this->view('categories.index', [
            'title'      => 'Categorias',
            'categories' => $categories,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $parentCategories = $this->categoryModel->getRoots();

        $this->view('categories.form', [
            'title'    => 'Nueva Categoria',
            'category' => null,
            'parents'  => $parentCategories,
        ]);
    }

    /**
     * Store new category
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/categories');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/categories/create');
        }

        $validator = Validator::make($_POST);
        $validator->required(['name'])
                  ->minLength(['name' => 2])
                  ->maxLength(['name' => 100, 'description' => 500]);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/categories/create');
        }

        $categoryId = $this->categoryModel->create([
            'name'        => $this->sanitize($_POST['name']),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'parent_id'   => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Categoria creada exitosamente');
        $this->redirect('/categories');
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $category = $this->categoryModel->find((int) $id);

        if (!$category) {
            $this->setFlash('error', 'Categoria no encontrada');
            $this->redirect('/categories');
        }

        $parentCategories = array_filter(
            $this->categoryModel->getRoots(),
            fn($c) => $c['id'] != $id
        );

        $this->view('categories.form', [
            'title'    => 'Editar Categoria',
            'category' => $category,
            'parents'  => $parentCategories,
        ]);
    }

    /**
     * Update category
     */
    public function update(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/categories');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/categories/edit/' . $id);
        }

        $validator = Validator::make($_POST);
        $validator->required(['name'])
                  ->minLength(['name' => 2]);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/categories/edit/' . $id);
        }

        $this->categoryModel->update((int) $id, [
            'name'        => $this->sanitize($_POST['name']),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'parent_id'   => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
        ]);

        $this->setFlash('success', 'Categoria actualizada exitosamente');
        $this->redirect('/categories');
    }

    /**
     * Delete category
     */
    public function delete(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/categories');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/categories');
        }

        try {
            $this->categoryModel->safeDelete((int) $id);
            $this->setFlash('success', 'Categoria eliminada exitosamente');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/categories');
    }
}
