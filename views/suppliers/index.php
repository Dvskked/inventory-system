<div class="page-header">
    <h2>Proveedores</h2>
    <a href="/suppliers/create" class="btn btn-primary">+ Nuevo Proveedor</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <?php if (empty($suppliers)): ?>
            <div class="empty-state">
                <p>No hay proveedores registrados</p>
                <a href="/suppliers/create" class="btn btn-primary">Crear Primer Proveedor</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($supplier['name']) ?></strong></td>
                        <td><?= htmlspecialchars($supplier['contact']) ?></td>
                        <td><?= htmlspecialchars($supplier['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($supplier['phone'] ?? '-') ?></td>
                        <td><span class="badge badge-info"><?= $supplier['product_count'] ?></span></td>
                        <td>
                            <span class="badge badge-<?= $supplier['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $supplier['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/suppliers/edit/<?= $supplier['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <form method="POST" action="/suppliers/delete/<?= $supplier['id'] ?>" 
                                      style="display:inline" onsubmit="return confirm('Eliminar este proveedor?');">
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
