<div class="page-header">
    <h2>Clientes</h2>
    <a href="/customers/create" class="btn btn-primary">+ Nuevo Cliente</a>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/customers" class="filters-form">
            <div class="form-row">
                <div class="form-group flex-2">
                    <input type="text" name="search" placeholder="Buscar por nombre, email o RFC..."
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <p>No se encontraron clientes</p>
                <a href="/customers/create" class="btn btn-primary">Registrar Primer Cliente</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>RFC</th>
                        <th>Compras</th>
                        <th>Total Gastado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($customer['name']) ?></strong></td>
                        <td><?= htmlspecialchars($customer['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($customer['phone'] ?? '-') ?></td>
                        <td><code><?= htmlspecialchars($customer['rfc'] ?? '-') ?></code></td>
                        <td><?= $customer['total_purchases'] ?? 0 ?></td>
                        <td class="text-success">$<?= number_format($customer['total_spent'] ?? 0, 2) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="/customers/<?= $customer['id'] ?>" class="btn btn-sm">Ver</a>
                                <a href="/customers/edit/<?= $customer['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
