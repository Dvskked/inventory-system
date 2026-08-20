<?php
$success = $_SESSION['flash']['success'] ?? null;
unset($_SESSION['flash']['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - InventoryFlow</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-logo">InventoryFlow</h1>
                <p class="auth-subtitle">Sistema de Gestion de Inventarios</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash']['error']) ?></div>
                <?php unset($_SESSION['flash']['error']); ?>
            <?php endif; ?>

            <form method="POST" action="/login" class="auth-form">
                <?= \InventoryFlow\Helpers\CSRF::field() ?>

                <div class="form-group">
                    <label for="email">Correo Electronico</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           autocomplete="email"
                           placeholder="admin@inventoryflow.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contrasena</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Ingresa tu contrasena">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesion
                </button>
            </form>

            <div class="auth-footer">
                <p>No tienes cuenta? <a href="/register">Registrate aqui</a></p>
            </div>
        </div>
    </div>
</body>
</html>
