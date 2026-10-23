<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../config/Conexion.php';

if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'logeado' || (($_SESSION['usuario_actual']['rol'] ?? '') !== 'admin')) {
    header("Location: /hotel2/views/auth/login.php?msg=acceso_denegado");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();
$error = "";

$res_editar = null;
if (isset($_GET['editar'])) {
    $id_edit = $_GET['editar'];
    $stmtEdit = $db->prepare("SELECT * FROM reservas WHERE id = ?");
    $stmtEdit->execute([$id_edit]);
    $res_editar = $stmtEdit->fetch();
}

if (es_post()) {
    try {
        validar_campos_vacios(['ci_usuario', 'numero_hab', 'fecha_in', 'fecha_out', 'estado_pago']);

        $id_reserva = trim($_POST['id_reserva']);
        $ci_usuario = trim($_POST['ci_usuario']);
        $numero_hab = (int)$_POST['numero_hab'];
        $fecha_in = trim($_POST['fecha_in']);
        $fecha_out = trim($_POST['fecha_out']);
        $estado_pago = trim($_POST['estado_pago']);

        validar_fechas_reserva($fecha_in, $fecha_out);

        if (empty($id_reserva)) {
            $id_reserva = 'RES-' . strtoupper(substr(uniqid(), -6));
            $stmt = $db->prepare("INSERT INTO reservas (id, ci_usuario, numero_hab, fecha_in, fecha_out, estado_pago) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_reserva, $ci_usuario, $numero_hab, $fecha_in, $fecha_out, $estado_pago]);
        } else {
            $stmt = $db->prepare("UPDATE reservas SET ci_usuario = ?, numero_hab = ?, fecha_in = ?, fecha_out = ?, estado_pago = ? WHERE id = ?");
            $stmt->execute([$ci_usuario, $numero_hab, $fecha_in, $fecha_out, $estado_pago, $id_reserva]);
        }

        header("Location: reservas.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$reservas = $db->query("SELECT r.*, u.nombres, u.apellidos FROM reservas r INNER JOIN usuarios u ON r.ci_usuario = u.ci ORDER BY r.fecha_in DESC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<h2>gestión de reservas</h2>

<?php if (!empty($error)): ?>
    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="auth-card" style="max-width: 500px; margin-bottom: 30px; width: 100%;">
    <h3><?php echo $res_editar ? "editar reserva: " . htmlspecialchars($res_editar['id']) : "registrar reserva manual"; ?></h3>
    
    <form method="POST" class="auth-form" style="margin-top: 15px;">
        <input type="hidden" name="id_reserva" value="<?php echo $res_editar ? htmlspecialchars($res_editar['id']) : ''; ?>">
        
        <div class="form-group">
            <label>cédula del cliente (ci):</label>
            <input type="text" name="ci_usuario" value="<?php echo $res_editar ? htmlspecialchars($res_editar['ci_usuario']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>número de habitación:</label>
            <input type="number" name="numero_hab" value="<?php echo $res_editar ? htmlspecialchars($res_editar['numero_hab']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>fecha de ingreso:</label>
            <input type="date" name="fecha_in" value="<?php echo $res_editar ? htmlspecialchars($res_editar['fecha_in']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>fecha de salida:</label>
            <input type="date" name="fecha_out" value="<?php echo $res_editar ? htmlspecialchars($res_editar['fecha_out']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>estado del pago:</label>
            <select name="estado_pago" style="width:100%; padding:10px; border-radius:6px; border:1px solid #f2dede;" required>
                <option value="pendiente" <?php echo ($res_editar && $res_editar['estado_pago'] == 'pendiente') ? 'selected' : ''; ?>>pendiente</option>
                <option value="pagado" <?php echo ($res_editar && $res_editar['estado_pago'] == 'pagado') ? 'selected' : ''; ?>>pagado</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <button type="submit" class="btn-submit" style="flex: 1;">
                <?php echo $res_editar ? "guardar cambios" : "crear reserva"; ?>
            </button>
            <?php if ($res_editar): ?>
                <a href="reservas.php" class="btn-logout" style="flex: 1; text-align: center; display: inline-block; padding: 10px;">cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<h3>todas las reservas</h3>
<div class="table-container" style="margin-bottom: 40px;">
    <table class="tabla-reservas">
        <thead>
            <tr>
                <th>id</th>
                <th>cliente</th>
                <th>ci</th>
                <th>hab.</th>
                <th>ingreso</th>
                <th>salida</th>
                <th>pago</th>
                <th>acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reservas)): ?>
                <tr><td colspan="8" style="text-align: center; color: #666;">no hay reservas registradas.</td></tr>
            <?php else: ?>
                <?php foreach ($reservas as $res): ?>
                <tr>
                    <td><code style="color: #888;"><?php echo htmlspecialchars($res['id']); ?></code></td>
                    <td><?php echo htmlspecialchars($res['nombres'] . ' ' . $res['apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($res['ci_usuario']); ?></td>
                    <td><strong>#<?php echo htmlspecialchars($res['numero_hab']); ?></strong></td>
                    <td><?php echo htmlspecialchars($res['fecha_in']); ?></td>
                    <td><?php echo htmlspecialchars($res['fecha_out']); ?></td>
                    <td>
                        <span class="badge <?php echo $res['estado_pago'] == 'pagado' ? 'pagado' : 'pendiente'; ?>">
                            <?php echo htmlspecialchars($res['estado_pago']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="reservas.php?editar=<?php echo urlencode($res['id']); ?>" class="btn-pagar">editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>