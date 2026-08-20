<div class="page-header">
    <h2>Punto de Venta</h2>
    <a href="/sales" class="btn">Historial de Ventas</a>
</div>

<div class="pos-layout">
    <!-- Product Search & Selection -->
    <div class="pos-products">
        <div class="card">
            <div class="card-header">
                <h3>Productos</h3>
            </div>
            <div class="card-body">
                <input type="text" id="productSearch" placeholder="Buscar producto..." class="form-input">
                
                <div class="product-grid" id="productList">
                    <?php foreach ($products as $product): ?>
                    <?php if ($product['stock'] > 0): ?>
                    <div class="product-card" 
                         data-id="<?= $product['id'] ?>"
                         data-name="<?= htmlspecialchars($product['name']) ?>"
                         data-sku="<?= htmlspecialchars($product['sku']) ?>"
                         data-price="<?= $product['price'] ?>"
                         data-stock="<?= $product['stock'] ?>">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-sku"><?= htmlspecialchars($product['sku']) ?></div>
                        <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="product-stock">Stock: <?= $product['stock'] ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart / Sale Summary -->
    <div class="pos-cart">
        <div class="card">
            <div class="card-header">
                <h3>Carrito de Venta</h3>
                <span class="ticket-number" id="ticketNumber"><?= $ticket ?></span>
            </div>
            <div class="card-body">
                <!-- Customer Selection -->
                <div class="form-group">
                    <label>Cliente (opcional)</label>
                    <select id="customerId">
                        <option value="">Publico General</option>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cart Items -->
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart">
                        <p>Agrega productos desde la lista</p>
                    </div>
                </div>

                <!-- Totals -->
                <div class="cart-totals" id="cartTotals" style="display:none">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Descuento:</span>
                        <div class="discount-input">
                            <input type="number" id="discountPercent" min="0" max="100" value="0" step="1">
                            <span>%</span>
                        </div>
                    </div>
                    <div class="total-row">
                        <span>IVA (16%):</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="total-row total-final">
                        <span>TOTAL:</span>
                        <span id="total">$0.00</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="cart-actions" id="cartActions" style="display:none">
                    <button type="button" class="btn btn-success btn-block" id="btnCompleteSale" onclick="completeSale()">
                        Completar Venta
                    </button>
                    <button type="button" class="btn btn-danger" onclick="clearCart()">
                        Vaciar Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const TAX_RATE = 0.16;
let cart = [];

function addToCart(id, name, sku, price, stock) {
    const existing = cart.find(item => item.id === id);
    
    if (existing) {
        if (existing.quantity < stock) {
            existing.quantity++;
        } else {
            alert('No hay mas stock disponible');
            return;
        }
    } else {
        cart.push({ id, name, sku, price: parseFloat(price), quantity: 1, stock: parseInt(stock) });
    }
    
    updateCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCart();
}

function updateQuantity(id, delta) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += delta;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
            alert('Stock maximo alcanzado');
        }
    }
    updateCart();
}

function clearCart() {
    cart = [];
    updateCart();
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const cartTotals = document.getElementById('cartTotals');
    const cartActions = document.getElementById('cartActions');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="empty-cart"><p>Agrega productos desde la lista</p></div>';
        cartTotals.style.display = 'none';
        cartActions.style.display = 'none';
        return;
    }
    
    cartTotals.style.display = 'block';
    cartActions.style.display = 'flex';
    
    let html = '<table class="cart-table"><thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th><th></th></tr></thead><tbody>';
    
    let subtotal = 0;
    
    cart.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        subtotal += itemSubtotal;
        
        html += `
        <tr>
            <td>${item.name}</td>
            <td>
                <button onclick="updateQuantity(${item.id}, -1)">-</button>
                ${item.quantity}
                <button onclick="updateQuantity(${item.id}, 1)">+</button>
            </td>
            <td>$${item.price.toFixed(2)}</td>
            <td>$${itemSubtotal.toFixed(2)}</td>
            <td><button onclick="removeFromCart(${item.id})" class="btn-remove">X</button></td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    cartItems.innerHTML = html;
    
    updateTotals();
}

function updateTotals() {
    const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
    
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    
    const discount = subtotal * (discountPercent / 100);
    const taxable = subtotal - discount;
    const tax = taxable * TAX_RATE;
    const total = taxable + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

document.getElementById('discountPercent').addEventListener('input', updateTotals);

function completeSale() {
    if (cart.length === 0) {
        alert('Agrega productos al carrito');
        return;
    }
    
    let subtotal = 0;
    cart.forEach(item => { subtotal += item.price * item.quantity; });
    
    const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
    const discount = subtotal * (discountPercent / 100);
    const taxable = subtotal - discount;
    const tax = taxable * TAX_RATE;
    const total = taxable + tax;
    
    const saleData = {
        customer_id: document.getElementById('customerId').value || null,
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        subtotal: subtotal,
        discount: discount,
        tax: tax,
        total: total,
        notes: ''
    };
    
    fetch('/sales/store', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(saleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venta #' + data.sale_id + ' registrada exitosamente!');
            cart = [];
            updateCart();
            document.getElementById('discountPercent').value = 0;
            // Reload to get new ticket number
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error de conexion: ' + error.message);
    });
}

// Product search filter
document.getElementById('productSearch').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const sku = card.dataset.sku.toLowerCase();
        card.style.display = (name.includes(term) || sku.includes(term)) ? 'block' : 'none';
    });
});

// Click on product card to add to cart
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function() {
        addToCart(
            this.dataset.id,
            this.dataset.name,
            this.dataset.sku,
            this.dataset.price,
            this.dataset.stock
        );
    });
});
</script>
