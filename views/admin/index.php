<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../config/Conexion.php';

if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'logeado' || (($_SESSION['usuario_actual']['rol'] ?? '') !== 'admin')) {
    header("Location: /hotel2/views/auth/login.php?msg=acceso_denegado");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();

$cant_usuarios = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$cant_habitaciones = $db->query("SELECT COUNT(*) FROM habitaciones")->fetchColumn();
$cant_reservas = $db->query("SELECT COUNT(*) FROM reservas")->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<h2>panel de administrador</h2>
<p class="auth-subtitle" style="margin-bottom: 25px;">bienvenido al panel de control general de skibidiHOTEL</p>

<div class="grid-habitaciones" style="grid-template-columns: repeat(3, 1fr); gap: 20px;">

    <div class="card" style="text-align: center; padding: 25px;">
        <h3 style="color: #872525; margin-bottom: 10px;">Usuarios</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: #a83232; margin: 10px 0;">
            <?php echo $cant_usuarios; ?>
        </p>
        <p style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">clientes y administradores</p>
        <a href="usuarios.php" class="btn-reservar">gestionar usuarios</a>
    </div>

    <div class="card" style="text-align: center; padding: 25px;">
        <h3 style="color: #872525; margin-bottom: 10px;">Habitaciones</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: #a83232; margin: 10px 0;">
            <?php echo $cant_habitaciones; ?>
        </p>
        <p style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">crear, editar y estados</p>
        <a href="habitaciones.php" class="btn-reservar">gestionar habitaciones</a>
    </div>

    <div class="card" style="text-align: center; padding: 25px;">
        <h3 style="color: #872525; margin-bottom: 10px;">Reservas</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: #a83232; margin: 10px 0;">
            <?php echo $cant_reservas; ?>
        </p>
        <p style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">historial y estados de pago</p>
        <a href="reservas.php" class="btn-reservar">ver reservas</a>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>