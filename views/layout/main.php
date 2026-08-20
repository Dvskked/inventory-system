<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="InventoryFlow - Sistema de Gestion de Inventarios y Ventas">
    <title><?= htmlspecialchars($title ?? 'InventoryFlow') ?> - InventoryFlow</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php if (\InventoryFlow\Helpers\Auth::check()): ?>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="logo">
                    <span class="logo-icon">&#9776;</span>
                    InventoryFlow
                </h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/dashboard" class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/dashboard' ? 'active' : '' ?>">
                            <span class="nav-icon">&#9632;</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/products" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/products') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9733;</span>
                            Productos
                        </a>
                    </li>
                    <li>
                        <a href="/categories" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/categories') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9776;</span>
                            Categorias
                        </a>
                    </li>
                    <li>
                        <a href="/suppliers" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/suppliers') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9879;</span>
                            Proveedores
                        </a>
                    </li>
                    <li>
                        <a href="/customers" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/customers') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9787;</span>
                            Clientes
                        </a>
                    </li>
                    <li>
                        <a href="/sales" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/sales') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9733;</span>
                            Ventas
                        </a>
                    </li>
                    <li>
                        <a href="/reports" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/reports') ? 'active' : '' ?>">
                            <span class="nav-icon">&#9632;</span>
                            Reportes
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                        <span class="user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></span>
                    </div>
                </div>
                <a href="/logout" class="btn-logout">Cerrar Sesion</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <button class="menu-toggle" id="menuToggle">&#9776;</button>
                <h1 class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                <div class="top-actions">
                    <span class="date-display"><?= date('d/m/Y') ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <?php if (!empty($_SESSION['flash'])): ?>
                    <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                        <div class="alert alert-<?= $type ?>" id="flash-<?= $type ?>">
                            <?= htmlspecialchars($message) ?>
                            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </main>
    </div>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>

    <script src="/assets/js/app.js"></script>
</body>
</html>
