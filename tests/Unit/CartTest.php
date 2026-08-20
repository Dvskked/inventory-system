<?php

declare(strict_types=1);

namespace InventoryFlow\Tests\Unit;

use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private array $cart = [];

    protected function setUp(): void
    {
        $this->cart = [];
    }

    private function addToCart(int $id, string $name, float $price, int $quantity, int $stock): void
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $id) {
                $item['quantity'] = min($item['quantity'] + $quantity, $item['stock']);
                return;
            }
        }
        $this->cart[] = [
            'id' => $id, 'name' => $name, 'price' => $price,
            'quantity' => min($quantity, $stock), 'stock' => $stock,
        ];
    }

    private function removeFromCart(int $id): void
    {
        $this->cart = array_values(array_filter($this->cart, fn($i) => $i['id'] !== $id));
    }

    private function getCartTotal(): float
    {
        $total = 0.0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    private function calculateTax(float $subtotal, float $discount = 0): array
    {
        $taxRate = 0.16;
        $taxable = $subtotal - $discount;
        $tax = $taxable * $taxRate;
        return ['subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'total' => $taxable + $tax];
    }

    public function testAddItemToCart(): void
    {
        $this->addToCart(1, 'Laptop', 1000.00, 1, 10);
        $this->assertCount(1, $this->cart);
        $this->assertEquals(1, $this->cart[0]['quantity']);
    }

    public function testDuplicateItemIncreasesQuantity(): void
    {
        $this->addToCart(1, 'Laptop', 1000.00, 1, 10);
        $this->addToCart(1, 'Laptop', 1000.00, 2, 10);
        $this->assertCount(1, $this->cart);
        $this->assertEquals(3, $this->cart[0]['quantity']);
    }

    public function testCannotExceedStock(): void
    {
        $this->addToCart(1, 'Laptop', 1000.00, 5, 3);
        $this->assertEquals(3, $this->cart[0]['quantity']);
    }

    public function testRemoveItemFromCart(): void
    {
        $this->addToCart(1, 'Laptop', 1000.00, 1, 10);
        $this->addToCart(2, 'Mouse', 50.00, 2, 20);
        $this->removeFromCart(1);
        $this->assertCount(1, $this->cart);
    }

    public function testCartTotal(): void
    {
        $this->addToCart(1, 'Laptop', 1000.00, 1, 10);
        $this->addToCart(2, 'Mouse', 50.00, 2, 20);
        $this->assertEquals(1100.00, $this->getCartTotal());
    }

    public function testEmptyCartTotal(): void
    {
        $this->assertEquals(0, $this->getCartTotal());
    }

    public function testCalculateTax(): void
    {
        $result = $this->calculateTax(1000.00, 100.00);
        $this->assertEquals(144.00, $result['tax']);
        $this->assertEquals(1044.00, $result['total']);
    }

    public function testTaxWithoutDiscount(): void
    {
        $result = $this->calculateTax(500.00);
        $this->assertEquals(80.00, $result['tax']);
        $this->assertEquals(580.00, $result['total']);
    }

    public function testCartWithMultipleItems(): void
    {
        $this->addToCart(1, 'Laptop', 18999.99, 1, 5);
        $this->addToCart(2, 'Mouse', 1299.99, 2, 50);
        $this->addToCart(3, 'Teclado', 1599.99, 1, 30);

        $total = $this->getCartTotal();
        $this->assertEquals(22899.96, $total, '', 0.01);
    }

    public function testDiscountPercentage(): void
    {
        $subtotal = 1000.00;
        $discountPercent = 10;
        $discount = $subtotal * ($discountPercent / 100);
        $this->assertEquals(100.00, $discount);
    }
}
