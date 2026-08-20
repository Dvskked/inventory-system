<div class="page-header">
    <h2>Productos</h2>
    <a href="/products/create" class="btn btn-primary">+ Nuevo Producto</a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/products" class="filters-form">
            <div class="form-row">
                <div class="form-group flex-2">
                    <input type="text" name="search" placeholder="Buscar por nombre o SKU..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <select name="category">
                        <option value="0">Todas las categorias</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h3>Lista de Productos (<?= number_format($total ?? 0) ?>)</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <p>No se encontraron productos</p>
                <a href="/products/create" class="btn btn-primary">Crear Primer Producto</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Proveedor</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr class="<?= $product['stock'] <= $product['min_stock'] ? 'table-warning' : '' ?>">
                        <td><code><?= htmlspecialchars($product['sku']) ?></code></td>
                        <td>
                            <a href="/products/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                        </td>
                        <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($product['supplier_name'] ?? '-') ?></td>
                        <td class="text-success">$<?= number_format($product['price'], 2) ?></td>
                        <td class="<?= $product['stock'] <= $product['min_stock'] ? 'text-danger' : '' ?>">
                            <?= $product['stock'] ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $product['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $product['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/products/<?= $product['id'] ?>" class="btn btn-sm" title="Ver">Ver</a>
                                <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning" title="Editar">Editar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($pagination && $pagination->totalPages > 1): ?>
            <div class="pagination-wrapper">
                <?= $pagination->render('/products?' . http_build_query(array_filter(['search' => $search, 'category' => $categoryId])) . '&page=') ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
