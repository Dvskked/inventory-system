<div class="page-header">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <div>
        <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-warning">Editar</a>
        <a href="/products" class="btn">Volver</a>
    </div>
</div>

<div class="product-detail">
    <div class="card">
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>SKU</label>
                    <span class="detail-value"><code><?= htmlspecialchars($product['sku']) ?></code></span>
                </div>
                <div class="detail-item">
                    <label>Estado</label>
                    <span class="badge badge-<?= $product['status'] === 'active' ? 'success' : 'danger' ?>">
                        <?= $product['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
                <div class="detail-item">
                    <label>Precio de Venta</label>
                    <span class="detail-value text-success">$<?= number_format($product['price'], 2) ?></span>
                </div>
                <div class="detail-item">
                    <label>Costo</label>
                    <span class="detail-value">$<?= number_format($product['cost'], 2) ?></span>
                </div>
                <div class="detail-item">
                    <label>Stock Actual</label>
                    <span class="detail-value <?= $product['stock'] <= $product['min_stock'] ? 'text-danger' : '' ?>">
                        <?= $product['stock'] ?> unidades
                    </span>
                </div>
                <div class="detail-item">
                    <label>Stock Minimo</label>
                    <span class="detail-value"><?= $product['min_stock'] ?> unidades</span>
                </div>
                <div class="detail-item">
                    <label>Categoria</label>
                    <span class="detail-value"><?= htmlspecialchars($product['category_name'] ?? '-') ?></span>
                </div>
                <div class="detail-item">
                    <label>Proveedor</label>
                    <span class="detail-value"><?= htmlspecialchars($product['supplier_name'] ?? '-') ?></span>
                </div>
            </div>

            <?php if (!empty($product['description'])): ?>
            <div class="detail-section">
                <h4>Descripcion</h4>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            <?php endif; ?>

            <div class="detail-section">
                <h4>Margen de Ganancia</h4>
                <?php
                    $margin = $product['price'] > 0 
                        ? (($product['price'] - $product['cost']) / $product['price']) * 100 
                        : 0;
                ?>
                <p class="<?= $margin > 30 ? 'text-success' : ($margin > 0 ? 'text-warning' : 'text-danger') ?>">
                    <?= number_format($margin, 1) ?>%
                    ($<?= number_format($product['price'] - $product['cost'], 2) ?> por unidad)
                </p>
            </div>

            <?php if ($product['stock'] <= $product['min_stock']): ?>
            <div class="alert alert-warning">
                &#9888; Este producto tiene stock bajo. Se recomienda reponer existencias.
            </div>
            <?php endif; ?>

            <div class="detail-meta">
                <span>Creado: <?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></span>
                <?php if (!empty($product['updated_at'])): ?>
                <span>Actualizado: <?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
