<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - InventoryFlow</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-logo">InventoryFlow</h1>
                <p class="auth-subtitle">Crear Nueva Cuenta</p>
            </div>

            <?php if (!empty($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash']['error']) ?></div>
                <?php unset($_SESSION['flash']['error']); ?>
            <?php endif; ?>

            <form method="POST" action="/register" class="auth-form">
                <?= \InventoryFlow\Helpers\CSRF::field() ?>

                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           minlength="3"
                           placeholder="Tu nombre completo"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electronico</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           placeholder="tu@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contrasena</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           minlength="6"
                           placeholder="Minimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contrasena</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="Repite tu contrasena">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Crear Cuenta
                </button>
            </form>

            <div class="auth-footer">
                <p>Ya tienes cuenta? <a href="/login">Inicia sesion</a></p>
            </div>
        </div>
    </div>
</body>
</html>
