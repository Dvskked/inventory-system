<div class="page-header">
    <h2><?= htmlspecialchars($customer['name']) ?></h2>
    <div>
        <a href="/customers/edit/<?= $customer['id'] ?>" class="btn btn-warning">Editar</a>
        <a href="/customers" class="btn">Volver</a>
    </div>
</div>

<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value"><?= $customer['total_purchases'] ?? 0 ?></span>
            <span class="stat-label">Compras Realizadas</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value text-success">$<?= number_format($customer['total_spent'] ?? 0, 2) ?></span>
            <span class="stat-label">Total Gastado</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Informacion del Cliente</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label>Email</label>
                <span class="detail-value"><?= htmlspecialchars($customer['email'] ?? '-') ?></span>
            </div>
            <div class="detail-item">
                <label>Telefono</label>
                <span class="detail-value"><?= htmlspecialchars($customer['phone'] ?? '-') ?></span>
            </div>
            <div class="detail-item">
                <label>RFC</label>
                <span class="detail-value"><code><?= htmlspecialchars($customer['rfc'] ?? '-') ?></code></span>
            </div>
            <div class="detail-item">
                <label>Direccion</label>
                <span class="detail-value"><?= htmlspecialchars($customer['address'] ?? '-') ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Historial de Compras</h3>
    </div>
    <div class="card-body">
        <?php if (empty($purchases)): ?>
            <p class="text-muted">Este cliente no tiene compras registradas</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>IVA</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $sale): ?>
                    <tr>
                        <td><a href="/sales/<?= $sale['id'] ?>"><?= $sale['id'] ?></a></td>
                        <td><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
                        <td>$<?= number_format($sale['subtotal'], 2) ?></td>
                        <td><?= $sale['discount'] > 0 ? '$' . number_format($sale['discount'], 2) : '-' ?></td>
                        <td>$<?= number_format($sale['tax'], 2) ?></td>
                        <td class="text-success font-bold">$<?= number_format($sale['total'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= $sale['status'] === 'completed' ? 'success' : ($sale['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                <?= $sale['status'] === 'completed' ? 'Completada' : ($sale['status'] === 'cancelled' ? 'Cancelada' : 'Pendiente') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
