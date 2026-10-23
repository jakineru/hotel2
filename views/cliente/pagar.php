<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../config/Conexion.php';

requerir_login();

$id_reserva = $_GET['id'] ?? null;
if (!$id_reserva) {
    header("Location: mis_reservas.php");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();

$stmt = $db->prepare("SELECT r.*, h.precio FROM reservas r INNER JOIN habitaciones h ON r.numero_hab = h.numero WHERE r.id = ?");
$stmt->execute([$id_reserva]);
$reserva = $stmt->fetch();

if (!$reserva || $reserva['estado_pago'] === 'pagado') {
    header("Location: mis_reservas.php");
    exit;
}

$d1 = new DateTime($reserva['fecha_in']);
$d2 = new DateTime($reserva['fecha_out']);
$noches = $d1->diff($d2)->days;
$total = $noches * $reserva['precio'];

$error = "";

if (es_post()) {
    try {
        validar_campos_vacios(['tarjeta', 'vencimiento', 'cvv', 'cardholder']);

        $tarjeta = trim($_POST['tarjeta']);
        $vencimiento = trim($_POST['vencimiento']);
        $cvv = trim($_POST['cvv']);
        $cardholder = trim($_POST['cardholder']);

        validar_tarjeta($tarjeta);
        validar_vencimiento_tarjeta($vencimiento);
        validar_cvv($cvv);
        validar_cardholder($cardholder);

        $stmtUpdate = $db->prepare("UPDATE reservas SET estado_pago = 'pagado' WHERE id = ?");
        $stmtUpdate->execute([$id_reserva]);
        
        header("Location: mis_reservas.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="auth-container" style="min-height: auto; padding: 15px 0;">
    <div class="auth-card" style="max-width: 480px;">
        <h2>procesar pago</h2>
        <p class="auth-subtitle">reserva #<?php echo htmlspecialchars($id_reserva); ?></p>

        <div class="monto-destacado">
            <span>SE DEBITARÁN</span>
            <strong>$<?php echo number_format($total, 2); ?></strong>
            <span>A TU CUENTA</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>número de tarjeta</label>
                <input type="text" name="tarjeta" inputmode="numeric" pattern="[0-9]{13,19}" maxlength="19" placeholder="4300XXXXXXXXXXXX" value="<?php echo $_POST['tarjeta'] ?? ''; ?>" required>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>vencimiento (MM/YY)</label>
                    <input type="text" name="vencimiento" placeholder="MM/YY" maxlength="5" value="<?php echo $_POST['vencimiento'] ?? ''; ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CVV</label>
                    <input type="password" name="cvv" inputmode="numeric" pattern="[0-9]{3,4}" maxlength="4" placeholder="123" required>
                </div>
            </div>

            <div class="form-group">
                <label>titular de la tarjeta (cardholder)</label>
                <input type="text" name="cardholder" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="nombre y apellido" value="<?php echo $_POST['cardholder'] ?? ''; ?>" required>
            </div>

            <button type="submit" class="btn-submit">pagar ahora</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>