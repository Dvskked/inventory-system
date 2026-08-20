<div class="page-header">
    <h2><?= $category ? 'Editar' : 'Nueva' ?> Categoria</h2>
    <a href="/categories" class="btn">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/categories/<?= $category ? 'update/' . $category['id'] : 'store' ?>" class="form">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>

            <div class="form-group">
                <label for="name">Nombre *</label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="100"
                       value="<?= htmlspecialchars($category['name'] ?? $_POST['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Descripcion</label>
                <textarea id="description" name="description" rows="3" maxlength="500"><?= htmlspecialchars($category['description'] ?? $_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="parent_id">Categoria Padre</label>
                <select id="parent_id" name="parent_id">
                    <option value="">Ninguna (Categoria raiz)</option>
                    <?php foreach ($parents as $parent): ?>
                    <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? $_POST['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($parent['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $category ? 'Actualizar' : 'Crear' ?> Categoria</button>
                <a href="/categories" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</div>
