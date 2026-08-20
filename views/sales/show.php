<div class="page-header">
    <h2>Detalle de Venta #<?= $sale['id'] ?></h2>
    <div>
        <?php if ($sale['status'] === 'completed'): ?>
        <form method="POST" action="/sales/cancel/<?= $sale['id'] ?>" style="display:inline" 
              onsubmit="return confirm('Cancelar esta venta restaurara el stock. Continuar?');">
            <?= \InventoryFlow\Helpers\CSRF::field() ?>
            <button type="submit" class="btn btn-danger">Cancelar Venta</button>
        </form>
        <?php endif; ?>
        <a href="/sales" class="btn">Volver</a>
    </div>
</div>

<div class="sale-detail">
    <div class="card">
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Estado</label>
                    <span class="badge badge-<?= $sale['status'] === 'completed' ? 'success' : 'danger' ?>">
                        <?= $sale['status'] === 'completed' ? 'Completada' : 'Cancelada' ?>
                    </span>
                </div>
                <div class="detail-item">
                    <label>Fecha</label>
                    <span class="detail-value"><?= date('d/m/Y H:i:s', strtotime($sale['created_at'])) ?></span>
                </div>
                <div class="detail-item">
                    <label>Cliente</label>
                    <span class="detail-value"><?= htmlspecialchars($sale['customer_name'] ?? 'Publico General') ?></span>
                </div>
                <div class="detail-item">
                    <label>Vendedor</label>
                    <span class="detail-value"><?= htmlspecialchars($sale['user_name']) ?></span>
                </div>
            </div>

            <h4 class="mt-4">Items de la Venta</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sale['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><code><?= htmlspecialchars($item['product_sku']) ?></code></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                        <td><strong>$<?= number_format($sale['subtotal'], 2) ?></strong></td>
                    </tr>
                    <?php if ($sale['discount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Descuento:</strong></td>
                        <td class="text-danger"><strong>-$<?= number_format($sale['discount'], 2) ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>IVA (16%):</strong></td>
                        <td><strong>$<?= number_format($sale['tax'], 2) ?></strong></td>
                    </tr>
                    <tr class="table-success">
                        <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                        <td><strong>$<?= number_format($sale['total'], 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <?php if (!empty($sale['notes'])): ?>
            <div class="detail-section mt-4">
                <h4>Notas</h4>
                <p><?= nl2br(htmlspecialchars($sale['notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
