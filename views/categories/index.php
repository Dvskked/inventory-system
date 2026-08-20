<div class="page-header">
    <h2>Categorias</h2>
    <a href="/categories/create" class="btn btn-primary">+ Nueva Categoria</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <p>No hay categorias registradas</p>
                <a href="/categories/create" class="btn btn-primary">Crear Primera Categoria</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category['id'] ?></td>
                        <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                        <td><?= htmlspecialchars(mb_substr($category['description'] ?? '', 0, 50)) ?></td>
                        <td>
                            <span class="badge badge-info"><?= $category['product_count'] ?></span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/categories/edit/<?= $category['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <form method="POST" action="/categories/delete/<?= $category['id'] ?>" 
                                      style="display:inline" onsubmit="return confirm('Eliminar esta categoria?');">
                                    <?= \InventoryFlow\Helpers\CSRF::field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
