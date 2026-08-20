<div class="page-header">
    <h2>Ventas</h2>
    <a href="/sales/create" class="btn btn-primary">+ Nueva Venta</a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/sales" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value"><?= $stats['total_sales'] ?? 0 ?></span>
            <span class="stat-label">Total Ventas</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value text-success">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></span>
            <span class="stat-label">Ingresos Totales</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value">$<?= number_format($stats['avg_sale'] ?? 0, 2) ?></span>
            <span class="stat-label">Ticket Promedio</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value">$<?= number_format($stats['total_discounts'] ?? 0, 2) ?></span>
            <span class="stat-label">Descuentos</span>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="card-header">
        <h3>Historial de Ventas</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($sales)): ?>
            <div class="empty-state">
                <p>No hay ventas registradas</p>
                <a href="/sales/create" class="btn btn-primary">Registrar Primera Venta</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr class="<?= $sale['status'] === 'cancelled' ? 'table-muted' : '' ?>">
                        <td><?= $sale['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
                        <td><?= htmlspecialchars($sale['customer_name'] ?? 'Publico General') ?></td>
                        <td><?= htmlspecialchars($sale['user_name'] ?? '-') ?></td>
                        <td>$<?= number_format($sale['subtotal'], 2) ?></td>
                        <td>$<?= number_format($sale['tax'], 2) ?></td>
                        <td class="text-success font-bold">$<?= number_format($sale['total'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= $sale['status'] === 'completed' ? 'success' : ($sale['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                <?= $sale['status'] === 'completed' ? 'Completada' : ($sale['status'] === 'cancelled' ? 'Cancelada' : 'Pendiente') ?>
                            </span>
                        </td>
                        <td>
                            <a href="/sales/<?= $sale['id'] ?>" class="btn btn-sm">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
