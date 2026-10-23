<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../config/Conexion.php';

if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'logeado' || (($_SESSION['usuario_actual']['rol'] ?? '') !== 'admin')) {
    header("Location: /hotel2/views/auth/login.php?msg=acceso_denegado");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();
$error = "";

$hab_editar = null;
if (isset($_GET['editar'])) {
    $num_edit = (int)$_GET['editar'];
    $stmtEdit = $db->prepare("SELECT * FROM habitaciones WHERE numero = ?");
    $stmtEdit->execute([$num_edit]);
    $hab_editar = $stmtEdit->fetch();
}

if (es_post()) {
    try {
        validar_campos_vacios(['numero', 'tipo', 'precio', 'capacidad', 'descripcion', 'estado', 'imagen']);

        $numero = (int)$_POST['numero'];
        $tipo = trim($_POST['tipo']);
        $precio = (float)$_POST['precio'];
        $capacidad = (int)$_POST['capacidad'];
        $descripcion = trim($_POST['descripcion']);
        $estado = trim($_POST['estado']);
        $imagen = trim($_POST['imagen']);
        $es_edicion = isset($_POST['es_edicion']) && $_POST['es_edicion'] === '1';

        if ($es_edicion) {
            $stmt = $db->prepare("UPDATE habitaciones SET tipo = ?, precio = ?, capacidad = ?, descripcion = ?, estado = ?, imagen = ? WHERE numero = ?");
            $stmt->execute([$tipo, $precio, $capacidad, $descripcion, $estado, $imagen, $numero]);
        } else {
            $stmtCheck = $db->prepare("SELECT numero FROM habitaciones WHERE numero = ?");
            $stmtCheck->execute([$numero]);
            if ($stmtCheck->fetch()) {
                throw new Exception("ya existe una habitación con el número $numero.");
            }

            $stmt = $db->prepare("INSERT INTO habitaciones (numero, tipo, precio, capacidad, descripcion, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$numero, $tipo, $precio, $capacidad, $descripcion, $estado, $imagen]);
        }

        header("Location: habitaciones.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$habitaciones = $db->query("SELECT * FROM habitaciones ORDER BY numero ASC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<h2>gestión de habitaciones</h2>

<?php if (!empty($error)): ?>
    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="auth-card" style="max-width: 500px; margin-bottom: 30px; width: 100%;">
    <h3><?php echo $hab_editar ? "editar habitación " . htmlspecialchars($hab_editar['numero']) : "agregar nueva habitación"; ?></h3>
    
    <form method="POST" class="auth-form" style="margin-top: 15px;">
        <?php if ($hab_editar): ?>
            <input type="hidden" name="es_edicion" value="1">
        <?php endif; ?>

        <div class="form-group">
            <label>número de habitación:</label>
            <input type="number" name="numero" value="<?php echo $hab_editar ? htmlspecialchars($hab_editar['numero']) : ''; ?>" placeholder="ej: 301" required <?php echo $hab_editar ? 'readonly style="background:#f5f5f5; color:#666;"' : ''; ?>>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>tipo:</label>
                <select name="tipo" style="width:100%; padding:10px; border-radius:6px; border:1px solid #f2dede;" required>
                    <option value="simple" <?php echo ($hab_editar && $hab_editar['tipo'] == 'simple') ? 'selected' : ''; ?>>simple</option>
                    <option value="doble" <?php echo ($hab_editar && $hab_editar['tipo'] == 'doble') ? 'selected' : ''; ?>>doble</option>
                    <option value="suite" <?php echo ($hab_editar && $hab_editar['tipo'] == 'suite') ? 'selected' : ''; ?>>suite</option>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>precio ($):</label>
                <input type="number" step="0.01" name="precio" value="<?php echo $hab_editar ? htmlspecialchars($hab_editar['precio']) : ''; ?>" placeholder="ej: 1500" required>
            </div>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>capacidad:</label>
                <input type="number" name="capacidad" value="<?php echo $hab_editar ? htmlspecialchars($hab_editar['capacidad']) : ''; ?>" placeholder="ej: 2" required>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>estado:</label>
                <select name="estado" style="width:100%; padding:10px; border-radius:6px; border:1px solid #f2dede;" required>
                    <option value="disponible" <?php echo ($hab_editar && $hab_editar['estado'] == 'disponible') ? 'selected' : ''; ?>>disponible</option>
                    <option value="mantenimiento" <?php echo ($hab_editar && $hab_editar['estado'] == 'mantenimiento') ? 'selected' : ''; ?>>mantenimiento</option>
                    <option value="inactiva" <?php echo ($hab_editar && $hab_editar['estado'] == 'inactiva') ? 'selected' : ''; ?>>inactiva</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>descripción:</label>
            <input type="text" name="descripcion" value="<?php echo $hab_editar ? htmlspecialchars($hab_editar['descripcion']) : ''; ?>" placeholder="breve descripción" required>
        </div>

        <div class="form-group">
            <label>imagen (ej: simple.jpg):</label>
            <input type="text" name="imagen" value="<?php echo $hab_editar ? htmlspecialchars($hab_editar['imagen']) : ''; ?>" placeholder="simple.jpg" required>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <button type="submit" class="btn-submit" style="flex: 1;">
                <?php echo $hab_editar ? "guardar cambios" : "crear habitación"; ?>
            </button>
            <?php if ($hab_editar): ?>
                <a href="habitaciones.php" class="btn-logout" style="flex: 1; text-align: center; display: inline-block; padding: 10px;">cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<h3>listado de habitaciones</h3>
<div class="table-container" style="margin-bottom: 40px;">
    <table class="tabla-reservas">
        <thead>
            <tr>
                <th>nro</th>
                <th>foto</th>
                <th>tipo</th>
                <th>precio</th>
                <th>capacidad</th>
                <th>estado</th>
                <th>acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($habitaciones as $hab): 
                $img_src = str_starts_with($hab['imagen'], '/') ? $hab['imagen'] : '/hotel2/img/' . $hab['imagen'];
            ?>
            <tr>
                <td><strong>#<?php echo htmlspecialchars($hab['numero']); ?></strong></td>
                <td><img src="<?php echo htmlspecialchars($img_src); ?>" width="80" height="50" style="object-fit:cover; border-radius:4px;"></td>
                <td><?php echo htmlspecialchars($hab['tipo']); ?></td>
                <td>$<?php echo number_format($hab['precio'], 2); ?></td>
                <td><?php echo htmlspecialchars($hab['capacidad']); ?> pers.</td>
                <td>
                    <span class="badge <?php echo $hab['estado'] == 'disponible' ? 'pagado' : 'pendiente'; ?>">
                        <?php echo htmlspecialchars($hab['estado']); ?>
                    </span>
                </td>
                <td>
                    <a href="habitaciones.php?editar=<?php echo $hab['numero']; ?>" class="btn-pagar">editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>