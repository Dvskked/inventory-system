<div class="page-header">
    <h2>Nuevo Producto</h2>
    <a href="/products" class="btn">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/products/store" class="form">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nombre del Producto *</label>
                    <input type="text" id="name" name="name" required minlength="3" 
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="sku">SKU *</label>
                    <input type="text" id="sku" name="sku" required 
                           pattern="[A-Za-z0-9\-]+" placeholder="Ej: ELEC-001"
                           value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descripcion</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Precio de Venta *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required
                           value="<?= $_POST['price'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="cost">Costo</label>
                    <input type="number" id="cost" name="cost" step="0.01" min="0"
                           value="<?= $_POST['cost'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="stock">Stock Inicial</label>
                    <input type="number" id="stock" name="stock" min="0" value="<?= $_POST['stock'] ?? '0' ?>">
                </div>
                <div class="form-group">
                    <label for="min_stock">Stock Minimo</label>
                    <input type="number" id="min_stock" name="min_stock" min="0" value="<?= $_POST['min_stock'] ?? '5' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="category_id">Categoria *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Seleccionar categoria</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
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
                        <option value="<?= $supplier['id'] ?>" <?= ($_POST['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <a href="/products" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</div>
