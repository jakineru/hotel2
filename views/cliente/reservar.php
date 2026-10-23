<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/Reserva.php';
require_once __DIR__ . '/../../config/Conexion.php';

requerir_login();

$error = "";
$numero_habitacion = $_GET['numero'] ?? null;

if (!$numero_habitacion) {
    header("Location: ../../index.php");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();
$stmt = $db->prepare("SELECT * FROM habitaciones WHERE numero = ?");
$stmt->execute([$numero_habitacion]);
$habitacion = $stmt->fetch();

if (!$habitacion) {
    header("Location: ../../index.php");
    exit;
}

$precio_por_noche = $habitacion['precio'];
$fecha_in = $_POST['fecha_in'] ?? date('Y-m-d');
$fecha_out = $_POST['fecha_out'] ?? date('Y-m-d', strtotime('+1 day'));

if (es_post()) {
    try {
        validar_campos_vacios(['fecha_in', 'fecha_out']);
        validar_fechas_reserva($fecha_in, $fecha_out);

        $datetime1 = new DateTime($fecha_in);
        $datetime2 = new DateTime($fecha_out);
        $noches = $datetime1->diff($datetime2)->days;

        if ($noches <= 0) {
            throw new Exception("la cantidad de noches debe ser mayor a cero.");
        }

        $ci_usuario = $_SESSION['usuario_actual']['ci'];
        $numero_hab = $habitacion['numero'];

        Reserva::crear($ci_usuario, $numero_hab, $fecha_in, $fecha_out);

        header("Location: mis_reservas.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$hoy = date('Y-m-d');
$min_out = date('Y-m-d', strtotime('+1 day'));

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="auth-container" style="min-height: auto; padding: 10px 0;">
    <div class="auth-card" style="max-width: 480px; padding: 0; overflow: hidden;">
        <div class="card-img-container" style="height: 200px;">
            <img src="<?php echo htmlspecialchars($habitacion['imagen']); ?>" alt="Habitación <?php echo htmlspecialchars($habitacion['numero']); ?>" class="card-img">
            <span class="badge-estado <?php echo strtolower($habitacion['estado']); ?>"><?php echo htmlspecialchars($habitacion['estado']); ?></span>
        </div>

        <div style="padding: 25px;">
            <h2>reservar habitación <?php echo htmlspecialchars($habitacion['numero']); ?></h2>
            <p class="auth-subtitle"><?php echo htmlspecialchars($habitacion['tipo']); ?> — $<span id="precio-noche"><?php echo htmlspecialchars($precio_por_noche); ?></span> / noche</p>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>fecha de inicio</label>
                    <input type="date" name="fecha_in" id="fecha_in" value="<?php echo htmlspecialchars($fecha_in); ?>" min="<?php echo $hoy; ?>" required>
                </div>

                <div class="form-group">
                    <label>fecha de salida</label>
                    <input type="date" name="fecha_out" id="fecha_out" value="<?php echo htmlspecialchars($fecha_out); ?>" min="<?php echo $min_out; ?>" required>
                </div>

                <div class="reserva-resumen">
                    <div class="resumen-item">
                        <span>noches de estadía:</span>
                        <strong id="val-noches">0</strong>
                    </div>
                    <div class="resumen-item total">
                        <span>total estimado:</span>
                        <strong>$<span id="val-total">0.00</span></strong>
                    </div>
                </div>

                <button type="submit" class="btn-submit">confirmar reserva</button>
            </form>
        </div>
    </div>
</div>

<script>
const precioNoche = parseFloat(document.getElementById('precio-noche').innerText);
const inputIn = document.getElementById('fecha_in');
const inputOut = document.getElementById('fecha_out');
const tdNoches = document.getElementById('val-noches');
const tdTotal = document.getElementById('val-total');

function calcular() {
    if (inputIn.value && inputOut.value) {
        const d1 = new Date(inputIn.value);
        const d2 = new Date(inputOut.value);
        
        if (inputIn.value) {
            const nextDay = new Date(d1);
            nextDay.setDate(nextDay.getDate() + 1);
            inputOut.min = nextDay.toISOString().split('T')[0];
        }

        const diffTime = d2 - d1;
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 0) {
            tdNoches.innerText = diffDays;
            tdTotal.innerText = (diffDays * precioNoche).toFixed(2);
        } else {
            tdNoches.innerText = 0;
            tdTotal.innerText = '0.00';
        }
    }
}

inputIn.addEventListener('change', () => {
    if (inputIn.value && inputOut.value && inputOut.value <= inputIn.value) {
        const d1 = new Date(inputIn.value);
        d1.setDate(d1.getDate() + 1);
        inputOut.value = d1.toISOString().split('T')[0];
    }
    calcular();
});

inputOut.addEventListener('change', calcular);
calcular();
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>