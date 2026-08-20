<div class="page-header">
    <h2><?= $customer ? 'Editar' : 'Nuevo' ?> Cliente</h2>
    <a href="/customers" class="btn">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/customers/<?= $customer ? 'update/' . $customer['id'] : 'store' ?>" class="form">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nombre Completo *</label>
                    <input type="text" id="name" name="name" required minlength="3" maxlength="200"
                           value="<?= htmlspecialchars($customer['name'] ?? $_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="rfc">RFC</label>
                    <input type="text" id="rfc" name="rfc" maxlength="20" placeholder="Formato: XXXX000000XXX"
                           value="<?= htmlspecialchars($customer['rfc'] ?? $_POST['rfc'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($customer['email'] ?? $_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group flex-1">
                    <label for="phone">Telefono</label>
                    <input type="text" id="phone" name="phone"
                           value="<?= htmlspecialchars($customer['phone'] ?? $_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Direccion</label>
                <textarea id="address" name="address" rows="2" maxlength="500"><?= htmlspecialchars($customer['address'] ?? $_POST['address'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $customer ? 'Actualizar' : 'Registrar' ?> Cliente</button>
                <a href="/customers" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</div>
