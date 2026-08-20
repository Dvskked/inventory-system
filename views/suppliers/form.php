<div class="page-header">
    <h2><?= $supplier ? 'Editar' : 'Nuevo' ?> Proveedor</h2>
    <a href="/suppliers" class="btn">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/suppliers/<?= $supplier ? 'update/' . $supplier['id'] : 'store' ?>" class="form">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nombre de la Empresa *</label>
                    <input type="text" id="name" name="name" required minlength="3" maxlength="200"
                           value="<?= htmlspecialchars($supplier['name'] ?? $_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="contact">Persona de Contacto *</label>
                    <input type="text" id="contact" name="contact" required minlength="3"
                           value="<?= htmlspecialchars($supplier['contact'] ?? $_POST['contact'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($supplier['email'] ?? $_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="phone">Telefono</label>
                    <input type="text" id="phone" name="phone"
                           value="<?= htmlspecialchars($supplier['phone'] ?? $_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Direccion</label>
                <textarea id="address" name="address" rows="2"><?= htmlspecialchars($supplier['address'] ?? $_POST['address'] ?? '') ?></textarea>
            </div>

            <?php if ($supplier): ?>
            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status">
                    <option value="active" <?= $supplier['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactive" <?= $supplier['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $supplier ? 'Actualizar' : 'Crear' ?> Proveedor</button>
                <a href="/suppliers" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</div>
