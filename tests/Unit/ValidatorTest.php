<?php

declare(strict_types=1);

namespace InventoryFlow\Tests\Unit;

use PHPUnit\Framework\TestCase;
use InventoryFlow\Helpers\Validator;

/**
 * Validator Test
 */
class ValidatorTest extends TestCase
{
    public function testRequiredFieldsPass(): void
    {
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $validator = Validator::make($data);
        $validator->required(['name', 'email']);

        $this->assertTrue($validator->passes());
    }

    public function testRequiredFieldsFail(): void
    {
        $data = ['name' => ''];
        $validator = Validator::make($data);
        $validator->required(['name', 'email']);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->getErrors());
        $this->assertArrayHasKey('email', $validator->getErrors());
    }

    public function testEmailValidation(): void
    {
        $validData = ['email' => 'test@example.com'];
        $invalidData = ['email' => 'not-an-email'];

        $validValidator = Validator::make($validData);
        $validValidator->email(['email']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->email(['email']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testMinLengthValidation(): void
    {
        $data = ['name' => 'AB'];
        $validator = Validator::make($data);
        $validator->minLength(['name' => 3]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('3', $validator->getError('name'));
    }

    public function testMinLengthPasses(): void
    {
        $data = ['name' => 'ABC'];
        $validator = Validator::make($data);
        $validator->minLength(['name' => 3]);

        $this->assertTrue($validator->passes());
    }

    public function testMaxLengthValidation(): void
    {
        $data = ['name' => str_repeat('A', 101)];
        $validator = Validator::make($data);
        $validator->maxLength(['name' => 100]);

        $this->assertTrue($validator->fails());
    }

    public function testNumericValidation(): void
    {
        $validData = ['price' => '19.99'];
        $invalidData = ['price' => 'abc'];

        $validValidator = Validator::make($validData);
        $validValidator->numeric(['price']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->numeric(['price']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testPositiveValidation(): void
    {
        $validData = ['price' => '19.99'];
        $negativeData = ['price' => '-5'];

        $validValidator = Validator::make($validData);
        $validValidator->positive(['price']);
        $this->assertTrue($validValidator->passes());

        $negativeValidator = Validator::make($negativeData);
        $negativeValidator->positive(['price']);
        $this->assertTrue($negativeValidator->fails());
    }

    public function testMatchesValidation(): void
    {
        $validData = ['password' => 'secret', 'password_confirmation' => 'secret'];
        $invalidData = ['password' => 'secret', 'password_confirmation' => 'different'];

        $validValidator = Validator::make($validData);
        $validValidator->matches(['password' => 'password_confirmation']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->matches(['password' => 'password_confirmation']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testInValidation(): void
    {
        $validData = ['status' => 'active'];
        $invalidData = ['status' => 'deleted'];

        $validValidator = Validator::make($validData);
        $validValidator->in(['status' => ['active', 'inactive']]);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->in(['status' => ['active', 'inactive']]);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testDateValidation(): void
    {
        $validData = ['date' => '2024-01-15'];
        $invalidData = ['date' => 'not-a-date'];

        $validValidator = Validator::make($validData);
        $validValidator->date(['date']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->date(['date']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testSkuValidation(): void
    {
        $validData = ['sku' => 'ELEC-001'];
        $invalidData = ['sku' => 'ELEC 001!'];

        $validValidator = Validator::make($validData);
        $validValidator->sku(['sku']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->sku(['sku']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testPhoneValidation(): void
    {
        $validData = ['phone' => '555-123-4567'];
        $invalidData = ['phone' => 'abc'];

        $validValidator = Validator::make($validData);
        $validValidator->phone(['phone']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->phone(['phone']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testRfcValidation(): void
    {
        $validData = ['rfc' => 'RAPR850101ABC'];
        $invalidData = ['rfc' => 'SHORT'];

        $validValidator = Validator::make($validData);
        $validValidator->rfc(['rfc']);
        $this->assertTrue($validValidator->passes());

        $invalidValidator = Validator::make($invalidData);
        $invalidValidator->rfc(['rfc']);
        $this->assertTrue($invalidValidator->fails());
    }

    public function testCustomValidation(): void
    {
        $data = ['age' => 15];
        $validator = Validator::make($data);
        $validator->custom('age', fn($value) => $value >= 18, 'Debe ser mayor de 18 anios');

        $this->assertTrue($validator->fails());
        $this->assertEquals('Debe ser mayor de 18 anios', $validator->getError('age'));
    }

    public function testFluentInterface(): void
    {
        $data = [
            'name' => 'Test Product',
            'email' => 'test@example.com',
            'price' => '19.99',
        ];

        $validator = Validator::make($data);
        $result = $validator->required(['name', 'email'])
                           ->email(['email'])
                           ->positive(['price']);

        $this->assertTrue($result->passes());
        $this->assertSame($validator, $result);
    }

    public function testGetFirstError(): void
    {
        $data = ['name' => '', 'email' => 'invalid'];
        $validator = Validator::make($data);
        $validator->required(['name', 'email'])
                  ->email(['email']);

        $firstError = $validator->getFirstError();
        $this->assertNotNull($firstError);
    }

    public function testEmptyDataFailsRequired(): void
    {
        $data = [];
        $validator = Validator::make($data);
        $validator->required(['name', 'email', 'price']);

        $this->assertTrue($validator->fails());
        $this->assertCount(3, $validator->getErrors());
    }
}
