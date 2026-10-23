<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$es_logeado = isset($_SESSION['estado']) && $_SESSION['estado'] === 'logeado';
$es_admin = $es_logeado && isset($_SESSION['usuario_actual']['rol']) && strtolower($_SESSION['usuario_actual']['rol']) === 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>skibidiHOTEL</title>
    <link rel="stylesheet" href="/hotel2/css/style.css">
</head>
<body>
    <header class="main-header">
        <nav class="nav-container">
            <div class="nav-section nav-left">
                <a href="/hotel2/index.php" class="nav-link">inicio</a>
                <?php if ($es_logeado): ?>
                    <?php if ($es_admin): ?>
                        <a href="/hotel2/views/admin/index.php" class="nav-btn-admin">⚙️ menú admin</a>
                    <?php else: ?>
                        <a href="/hotel2/views/cliente/mis_reservas.php" class="nav-link">mis reservas</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="nav-section nav-center">
                <a href="/hotel2/index.php" class="brand-logo">skibidi<span>HOTEL</span></a>
            </div>

            <div class="nav-section nav-right">
                <?php if ($es_logeado): ?>
                    <div class="user-info">
                        <span class="user-greeting">hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_actual']['nombres']); ?></strong></span>
                        <a href="/hotel2/logout.php" class="btn-logout">salir</a>
                    </div>
                <?php else: ?>
                    <a href="/hotel2/views/auth/login.php" class="nav-btn-ingresar">ingresar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>