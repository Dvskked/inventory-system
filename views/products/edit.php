<div class="page-header">
    <h2>Editar Producto</h2>
    <div>
        <a href="/products/<?= $product['id'] ?>" class="btn">Volver al Producto</a>
        <a href="/products" class="btn">Lista de Productos</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/products/update/<?= $product['id'] ?>" class="form">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nombre del Producto *</label>
                    <input type="text" id="name" name="name" required minlength="3"
                           value="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="sku">SKU *</label>
                    <input type="text" id="sku" name="sku" required 
                           pattern="[A-Za-z0-9\-]+"
                           value="<?= htmlspecialchars($product['sku']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descripcion</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Precio de Venta *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required
                           value="<?= $product['price'] ?>">
                </div>
                <div class="form-group">
                    <label for="cost">Costo</label>
                    <input type="number" id="cost" name="cost" step="0.01" min="0"
                           value="<?= $product['cost'] ?>">
                </div>
                <div class="form-group">
                    <label for="min_stock">Stock Minimo</label>
                    <input type="number" id="min_stock" name="min_stock" min="0"
                           value="<?= $product['min_stock'] ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="category_id">Categoria *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Seleccionar categoria</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="supplier_id">Proveedor</label>
                    <select id="supplier_id" name="supplier_id">
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['id'] ?>" <?= $product['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Estado</label>
                    <select id="status" name="status">
                        <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                <a href="/products/<?= $product['id'] ?>" class="btn">Cancelar</a>
            </div>
        </form>

        <hr>

        <div class="danger-zone">
            <h4>Zona de Peligro</h4>
            <form method="POST" action="/products/delete/<?= $product['id'] ?>" 
                  onsubmit="return confirm('Estas seguro de eliminar este producto? Esta accion no se puede deshacer.');">
                <?= \InventoryFlow\Helpers\CSRF::field() ?>
                <button type="submit" class="btn btn-danger">Eliminar Producto</button>
            </form>
        </div>
    </div>
</div>
