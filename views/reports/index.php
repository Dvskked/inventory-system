<div class="page-header">
    <h2>Reportes</h2>
</div>

<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value"><?= number_format($productStats['total'] ?? 0) ?></span>
            <span class="stat-label">Productos</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value">$<?= number_format($saleStats['total_revenue'] ?? 0, 2) ?></span>
            <span class="stat-label">Ingresos</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value"><?= number_format($customerStats['total'] ?? 0) ?></span>
            <span class="stat-label">Clientes</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-value"><?= number_format($categoryStats['total'] ?? 0) ?></span>
            <span class="stat-label">Categorias</span>
        </div>
    </div>
</div>

<div class="reports-grid">
    <!-- Sales Report -->
    <div class="card report-card">
        <div class="card-body">
            <h3>Reporte de Ventas</h3>
            <p>Resumen de ventas por periodo, top productos y analisis de ingresos.</p>
            <a href="/reports/sales" class="btn btn-primary">Ver Reporte</a>
        </div>
    </div>

    <!-- Inventory Report -->
    <div class="card report-card">
        <div class="card-body">
            <h3>Reporte de Inventario</h3>
            <p>Estado del inventario, productos con stock bajo y valorizacion.</p>
            <a href="/reports/inventory" class="btn btn-primary">Ver Reporte</a>
        </div>
    </div>

    <!-- Customer Report -->
    <div class="card report-card">
        <div class="card-body">
            <h3>Reporte de Clientes</h3>
            <p>Analisis de clientes, compras recurrentes y segmentacion.</p>
            <a href="/reports/customers" class="btn btn-primary">Ver Reporte</a>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card report-card">
        <div class="card-body">
            <h3>Exportar Datos</h3>
            <p>Descarga reportes en formato CSV para analisis externo.</p>
            <div class="export-buttons">
                <a href="/reports/export?type=products" class="btn">Productos CSV</a>
                <a href="/reports/export?type=sales" class="btn">Ventas CSV</a>
                <a href="/reports/export?type=customers" class="btn">Clientes CSV</a>
            </div>
        </div>
    </div>
</div>
