<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/Usuario.php';

$error = "";

if (es_post()) {
    try {
        validar_campos_vacios(['ci', 'nombres', 'apellidos', 'fecha_nacimiento', 'telefono', 'email', 'contra']);

        $ci = trim($_POST['ci']);
        $nombres = trim($_POST['nombres']);
        $apellidos = trim($_POST['apellidos']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $telefono = trim($_POST['telefono']);
        $email = trim($_POST['email']);
        $contra = $_POST['contra'];

        // validaciones de backend
        validar_ci($ci);
        validar_texto_sin_numeros($nombres, 'nombres');
        validar_texto_sin_numeros($apellidos, 'apellidos');
        validar_mayor_edad($fecha_nacimiento);
        validar_telefono($telefono);
        validar_formato_email($email);

        if (strlen($contra) < 8 || !preg_match('/[A-Za-z]/', $contra) || !preg_match('/[0-9]/', $contra)) {
            throw new Exception("la contraseña debe tener al menos 8 caracteres y contener letras y números.");
        }

        if (Usuario::obtenerPorCi($ci)) {
            throw new Exception("ya existe un usuario registrado con esa cédula.");
        }

        if (Usuario::obtenerPorEmail($email)) {
            throw new Exception("el correo electrónico ya está registrado.");
        }

        Usuario::crear($ci, $nombres, $apellidos, $fecha_nacimiento, $telefono, $email, $contra);

        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$max_fecha = date('Y-m-d', strtotime('-18 years'));

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 450px;">
        <h2>registro de cliente</h2>
        <p class="auth-subtitle">creá tu cuenta para realizar reservas</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>cédula / documento</label>
                <input type="text" name="ci" inputmode="numeric" pattern="[0-9]+" title="NÚMERO DE DOCUMENTO INVÁLIDO" value="<?php echo $_POST['ci'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>nombre/s</label>
                <input type="text" name="nombres" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="SOLO LETRAS" value="<?php echo $_POST['nombres'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>apellidos</label>
                <input type="text" name="apellidos" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="SOLO LETRAS" value="<?php echo $_POST['apellidos'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>fecha de nacimiento (mayor de 18 años)</label>
                <input type="date" name="fecha_nacimiento" max="<?php echo $max_fecha; ?>" value="<?php echo $_POST['fecha_nacimiento'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>teléfono de contacto</label>
                <input type="tel" name="telefono" pattern="[0-9\+\-\s]+" title="ingresá un número de teléfono válido" value="<?php echo $_POST['telefono'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>email</label>
                <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>contraseña (mínimo 8 caracteres, letras y números)</label>
                <input type="password" name="contra" minlength="8" pattern="[A-Za-z0-9]+" title="la contraseña debe contener al menos 8 caracteres, letras y números" required>
            </div>

            <button type="submit" class="btn-submit">registrarse</button>
        </form>

        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php" style="color: #a83232; font-size: 0.9rem; text-decoration: none; font-weight: 600;">¿ya tenés cuenta? iniciá sesión</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>