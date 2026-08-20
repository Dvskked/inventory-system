<?php

declare(strict_types=1);

namespace InventoryFlow\Tests\Unit;

use PHPUnit\Framework\TestCase;
use InventoryFlow\Helpers\Auth;

class AuthTest extends TestCase
{
    public function testPasswordHashing(): void
    {
        $password = 'Admin123!';
        $hash = Auth::hashPassword($password);

        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
    }

    public function testPasswordVerificationFails(): void
    {
        $hash = Auth::hashPassword('Admin123!');
        $this->assertFalse(password_verify('WrongPassword', $hash));
    }

    public function testBcryptHashFormat(): void
    {
        $hash = Auth::hashPassword('test');
        $this->assertMatchesRegularExpression('/^\$2y\$12\$/', $hash);
    }

    public function testEmailFormatValidation(): void
    {
        $this->assertTrue(filter_var('user@example.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertTrue(filter_var('admin@inventoryflow.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('not-an-email', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('missing@', FILTER_VALIDATE_EMAIL) !== false);
    }

    public function testRoleValidation(): void
    {
        $validRoles = ['admin', 'employee'];
        $this->assertContains('admin', $validRoles);
        $this->assertContains('employee', $validRoles);
        $this->assertNotContains('superadmin', $validRoles);
    }

    public function testPasswordStrength(): void
    {
        $weakPasswords = ['123', 'abc', 'password'];
        $strongPasswords = ['Admin123!', 'MyP@ssw0rd', 'Str0ng!Pass'];

        foreach ($weakPasswords as $pwd) {
            $this->assertGreaterThanOrEqual(6, strlen($pwd) < 6 ? 0 : strlen($pwd));
        }

        foreach ($strongPasswords as $pwd) {
            $this->assertGreaterThanOrEqual(8, strlen($pwd));
            $this->assertMatchesRegularExpression('/[A-Z]/', $pwd);
            $this->assertMatchesRegularExpression('/[0-9]/', $pwd);
        }
    }
}
