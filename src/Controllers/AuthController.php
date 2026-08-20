<?php

declare(strict_types=1);

namespace InventoryFlow\Controllers;

use InventoryFlow\Core\Controller;
use InventoryFlow\Helpers\Auth;
use InventoryFlow\Helpers\CSRF;
use InventoryFlow\Helpers\Validator;

/**
 * Authentication Controller
 */
class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth.login', [
            'title' => 'Iniciar Sesion',
        ]);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/login');
        }

        $email = $this->input('email', '');
        $password = $this->input('password', '');

        $validator = Validator::make($_POST);
        $validator->required(['email', 'password'])
                  ->email(['email']);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/login');
        }

        $user = Auth::attempt($email, $password);

        if ($user) {
            CSRF::rotate();
            $this->setFlash('success', "Bienvenido, {$user['name']}!");
            $this->redirect('/dashboard');
        }

        $this->setFlash('error', 'Credenciales incorrectas');
        $this->redirect('/login');
    }

    /**
     * Show registration form
     */
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth.register', [
            'title' => 'Crear Cuenta',
        ]);
    }

    /**
     * Process registration
     */
    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/register');
        }

        if (!CSRF::verify()) {
            $this->setFlash('error', 'Token de seguridad invalido');
            $this->redirect('/register');
        }

        $validator = Validator::make($_POST);
        $validator->required(['name', 'email', 'password', 'password_confirmation'])
                  ->email(['email'])
                  ->minLength(['name' => 3, 'password' => 6])
                  ->matches(['password' => 'password_confirmation']);

        if ($validator->fails()) {
            $this->setFlash('error', $validator->getFirstError());
            $this->redirect('/register');
        }

        // Check if email exists
        if (Auth::emailExists($_POST['email'])) {
            $this->setFlash('error', 'El correo ya esta registrado');
            $this->redirect('/register');
        }

        Auth::createUser([
            'name'     => $this->sanitize($_POST['name']),
            'email'    => $this->sanitize($_POST['email']),
            'password' => $_POST['password'],
            'role'     => 'employee',
        ]);

        $this->setFlash('success', 'Cuenta creada exitosamente. Inicia sesion.');
        $this->redirect('/login');
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        Auth::logout();
        $this->setFlash('success', 'Sesion cerrada correctamente');
        $this->redirect('/login');
    }
}
