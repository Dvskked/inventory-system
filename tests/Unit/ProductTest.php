<?php

declare(strict_types=1);

namespace InventoryFlow\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Product Model Test
 */
class ProductTest extends TestCase
{
    public function testSkuFormat(): void
    {
        $validSkus = ['ELEC-001', 'ROPA-002', 'ABC123', 'TEST-SKU-001'];
        $invalidSkus = ['ELEC 001', 'SKU!', 'no spaces'];

        foreach ($validSkus as $sku) {
            $this->assertMatchesRegularExpression(
                '/^[A-Z0-9\-]+$/i',
                $sku,
                "SKU '{$sku}' should be valid"
            );
        }

        foreach ($invalidSkus as $sku) {
            $this->assertDoesNotMatchRegularExpression(
                '/^[A-Z0-9\-]+$/i',
                $sku,
                "SKU '{$sku}' should be invalid"
            );
        }
    }

    public function testProductPriceCalculation(): void
    {
        $price = 100.00;
        $cost = 60.00;
        $quantity = 3;

        $total = $price * $quantity;
        $margin = (($price - $cost) / $price) * 100;

        $this->assertEquals(300.00, $total);
        $this->assertEquals(40.0, $margin);
    }

    public function testStockValidation(): void
    {
        $stock = 10;
        $quantityToSell = 5;
        $minStock = 5;

        $canSell = $stock >= $quantityToSell;
        $this->assertTrue($canSell);

        $newStock = $stock - $quantityToSell;
        $isLowStock = $newStock <= $minStock;
        $this->assertTrue($isLowStock);
    }

    public function testCannotSellMoreThanStock(): void
    {
        $stock = 5;
        $quantityToSell = 10;

        $canSell = $stock >= $quantityToSell;
        $this->assertFalse($canSell);
    }

    public function testOutOfStockDetection(): void
    {
        $stock = 0;
        $this->assertTrue($stock === 0);

        $stock = 5;
        $this->assertFalse($stock === 0);
    }

    public function testMarginCalculation(): void
    {
        $testCases = [
            ['price' => 100, 'cost' => 50, 'expected' => 50.0],
            ['price' => 200, 'cost' => 140, 'expected' => 30.0],
            ['price' => 50, 'cost' => 50, 'expected' => 0.0],
            ['price' => 100, 'cost' => 0, 'expected' => 100.0],
        ];

        foreach ($testCases as $case) {
            $margin = $case['price'] > 0
                ? (($case['price'] - $case['cost']) / $case['price']) * 100
                : 0;
            
            $this->assertEquals(
                $case['expected'],
                $margin,
                "Margin for price={$case['price']}, cost={$case['cost']}"
            );
        }
    }

    public function testSaleTotalCalculation(): void
    {
        $items = [
            ['price' => 100, 'quantity' => 2],
            ['price' => 50, 'quantity' => 3],
            ['price' => 25, 'quantity' => 4],
        ];

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 10;
        $taxRate = 0.16;
        $taxableAmount = $subtotal - $discount;
        $tax = $taxableAmount * $taxRate;
        $total = $taxableAmount + $tax;

        $this->assertEquals(450, $subtotal);
        $this->assertEquals(440, $taxableAmount);
        $this->assertEquals(70.4, $tax);
        $this->assertEquals(510.4, $total);
    }

    public function testTicketNumberFormat(): void
    {
        $date = date('Ymd');
        $count = 1;
        $ticketNumber = sprintf('TKT-%s-%04d', $date, $count);

        $this->assertMatchesRegularExpression('/^TKT-\d{8}-\d{4}$/', $ticketNumber);
    }
}
