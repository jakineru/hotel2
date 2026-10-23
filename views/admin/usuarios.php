<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../config/Conexion.php';
require_once __DIR__ . '/../../models/UsuarioFactory.php';

if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'logeado' || (($_SESSION['usuario_actual']['rol'] ?? '') !== 'admin')) {
    header("Location: /hotel2/views/auth/login.php?msg=acceso_denegado");
    exit;
}

$db = Conexion::obtenerInstancia()->getPdo();
$error = "";

$usr_editar = null;
if (isset($_GET['editar'])) {
    $ci_edit = $_GET['editar'];
    $stmtEdit = $db->prepare("SELECT * FROM usuarios WHERE ci = ?");
    $stmtEdit->execute([$ci_edit]);
    $usr_editar = $stmtEdit->fetch();
}

if (es_post()) {
    try {
        validar_campos_vacios(['ci', 'nombres', 'apellidos', 'email', 'contra', 'rol', 'estado']);

        $es_edicion = isset($_POST['es_edicion']) && $_POST['es_edicion'] === '1';

        validar_formato_email($_POST['email']);
        validar_texto_sin_numeros($_POST['nombres'], 'nombres');
        validar_texto_sin_numeros($_POST['apellidos'], 'apellidos');

        $datos_usuario = UsuarioFactory::crearUsuario($_POST['rol'], $_POST);
        $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;

        if ($es_edicion) {
            $stmt = $db->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, email = ?, contra = ?, rol = ?, estado = ?, fecha_nacimiento = ? WHERE ci = ?");
            $stmt->execute([
                $datos_usuario['nombres'],
                $datos_usuario['apellidos'],
                $datos_usuario['email'],
                $datos_usuario['contra'],
                $datos_usuario['rol'],
                $datos_usuario['estado'],
                $fecha_nacimiento,
                $datos_usuario['ci']
            ]);
        } else {
            $stmtCheck = $db->prepare("SELECT ci FROM usuarios WHERE ci = ?");
            $stmtCheck->execute([$datos_usuario['ci']]);
            if ($stmtCheck->fetch()) {
                throw new Exception("ya existe un usuario con la cédula {$datos_usuario['ci']}.");
            }

            $stmt = $db->prepare("INSERT INTO usuarios (ci, nombres, apellidos, email, contra, rol, estado, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos_usuario['ci'],
                $datos_usuario['nombres'],
                $datos_usuario['apellidos'],
                $datos_usuario['email'],
                $datos_usuario['contra'],
                $datos_usuario['rol'],
                $datos_usuario['estado'],
                $fecha_nacimiento
            ]);
        }

        header("Location: usuarios.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$usuarios = $db->query("SELECT * FROM usuarios ORDER BY ci ASC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<h2>gestión de usuarios</h2>

<?php if (!empty($error)): ?>
    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="auth-card" style="max-width: 500px; margin-bottom: 30px; width: 100%;">
    <h3><?php echo $usr_editar ? "editar usuario: " . htmlspecialchars($usr_editar['ci']) : "agregar nuevo usuario"; ?></h3>
    
    <form method="POST" class="auth-form" style="margin-top: 15px;">
        <?php if ($usr_editar): ?>
            <input type="hidden" name="es_edicion" value="1">
        <?php endif; ?>

        <div class="form-group">
            <label>cédula (ci):</label>
            <input type="text" name="ci" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['ci']) : ''; ?>" placeholder="ej: 12345678" required <?php echo $usr_editar ? 'readonly style="background:#f5f5f5; color:#666;"' : ''; ?>>
        </div>
        
        <div class="form-group">
            <label>nombres:</label>
            <input type="text" name="nombres" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['nombres']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>apellidos:</label>
            <input type="text" name="apellidos" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['apellidos']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>email:</label>
            <input type="email" name="email" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['email']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>contraseña:</label>
            <input type="text" name="contra" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['contra']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" value="<?php echo $usr_editar ? htmlspecialchars($usr_editar['fecha_nacimiento']) : ''; ?>">
        </div>
        
        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>rol:</label>
                <select name="rol" style="width:100%; padding:10px; border-radius:6px; border:1px solid #f2dede;" required>
                    <option value="cliente" <?php echo ($usr_editar && $usr_editar['rol'] == 'cliente') ? 'selected' : ''; ?>>cliente</option>
                    <option value="admin" <?php echo ($usr_editar && $usr_editar['rol'] == 'admin') ? 'selected' : ''; ?>>admin</option>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>estado:</label>
                <select name="estado" style="width:100%; padding:10px; border-radius:6px; border:1px solid #f2dede;" required>
                    <option value="activo" <?php echo ($usr_editar && $usr_editar['estado'] == 'activo') ? 'selected' : ''; ?>>activo</option>
                    <option value="inactivo" <?php echo ($usr_editar && $usr_editar['estado'] == 'inactivo') ? 'selected' : ''; ?>>inactivo</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <button type="submit" class="btn-submit" style="flex: 1;">
                <?php echo $usr_editar ? "guardar cambios" : "crear usuario"; ?>
            </button>
            <?php if ($usr_editar): ?>
                <a href="usuarios.php" class="btn-logout" style="flex: 1; text-align: center; display: inline-block; padding: 10px;">cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<h3>lista de usuarios</h3>
<div class="table-container" style="margin-bottom: 40px;">
    <table class="tabla-reservas">
        <thead>
            <tr>
                <th>ci</th>
                <th>nombre completo</th>
                <th>email</th>
                <th>rol</th>
                <th>estado</th>
                <th>acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usr): ?>
            <tr>
                <td><?php echo htmlspecialchars($usr['ci']); ?></td>
                <td><?php echo htmlspecialchars($usr['nombres'] . ' ' . $usr['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($usr['email']); ?></td>
                <td>
                    <span class="badge" style="background: <?php echo $usr['rol'] == 'admin' ? '#e3f2fd; color:#1565c0;' : '#f5f5f5; color:#616161;'; ?>">
                        <?php echo strtoupper($usr['rol']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?php echo $usr['estado'] == 'activo' ? 'pagado' : 'pendiente'; ?>">
                        <?php echo htmlspecialchars($usr['estado']); ?>
                    </span>
                </td>
                <td>
                    <a href="usuarios.php?editar=<?php echo urlencode($usr['ci']); ?>" class="btn-pagar">editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>