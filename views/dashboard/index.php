<div class="dashboard">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">&#9733;</div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($total ?? 0) ?></span>
                <span class="stat-label">Total Productos</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">&#9733;</div>
            <div class="stat-info">
                <span class="stat-value">$<?= number_format($saleStats['total_revenue'] ?? 0, 2) ?></span>
                <span class="stat-label">Ingresos Totales</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">&#9787;</div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($customerStats['total'] ?? 0) ?></span>
                <span class="stat-label">Clientes</span>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-icon stat-icon-orange">&#9888;</div>
            <div class="stat-info">
                <span class="stat-value"><?= count($lowStockProducts ?? []) ?></span>
                <span class="stat-label">Stock Bajo</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Recent Sales -->
        <div class="card">
            <div class="card-header">
                <h3>Ventas Recientes</h3>
                <a href="/sales" class="btn btn-sm">Ver Todas</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentSales)): ?>
                    <p class="text-muted">No hay ventas recientes</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                            <tr>
                                <td><?= $sale['id'] ?></td>
                                <td><?= htmlspecialchars($sale['customer_name'] ?? 'Publico General') ?></td>
                                <td class="text-success">$<?= number_format($sale['total'], 2) ?></td>
                                <td><?= date('d/m H:i', strtotime($sale['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="card">
            <div class="card-header">
                <h3>Productos Mas Vendidos</h3>
            </div>
            <div class="card-body">
                <?php if (empty($topSelling)): ?>
                    <p class="text-muted">No hay datos de ventas</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Vendidos</th>
                                <th>Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topSelling as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['total_sold'] ?></td>
                                <td class="text-success">$<?= number_format($product['total_revenue'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card card-warning">
            <div class="card-header">
                <h3>&#9888; Alerta de Stock Bajo</h3>
            </div>
            <div class="card-body">
                <?php if (empty($lowStockProducts)): ?>
                    <p class="text-success">Todos los productos tienen stock suficiente</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Minimo</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                            <tr class="<?= $product['stock'] === 0 ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td class="text-danger"><?= $product['stock'] ?></td>
                                <td><?= $product['min_stock'] ?></td>
                                <td>
                                    <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                                        Reponer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="card">
            <div class="card-header">
                <h3>Mejores Clientes</h3>
            </div>
            <div class="card-body">
                <?php if (empty($topCustomers)): ?>
                    <p class="text-muted">No hay datos de clientes</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Compras</th>
                                <th>Total Gastado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topCustomers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['name']) ?></td>
                                <td><?= $customer['total_purchases'] ?></td>
                                <td class="text-success">$<?= number_format($customer['total_spent'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
