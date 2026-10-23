<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/Usuario.php';

$error = "";

if (es_post()) {
    try {
        validar_campos_vacios(['ci', 'contra']);

        $ci = trim($_POST['ci']);
        $contra = $_POST['contra'];

        $usuario = Usuario::obtenerPorCi($ci);

        if (!$usuario || $usuario['contra'] !== $contra) {
            throw new Exception("los datos ingresados son incorrectos.");
        }

        if ($usuario['estado'] !== 'activo') {
            throw new Exception("usuario inactivo.");
        }

        $_SESSION['estado'] = 'logeado';
        $_SESSION['usuario_actual'] = $usuario;

        header("Location: ../../index.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>ingresar</h2>
        <p class="auth-subtitle">iniciá sesión para continuar</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>cédula (ci)</label>
                <input type="text" name="ci" value="<?php echo isset($_POST['ci']) ? htmlspecialchars($_POST['ci']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>contraseña</label>
                <input type="password" name="contra" required>
            </div>

            <button type="submit" class="btn-submit">entrar</button>
            
            <div style="text-align: center; margin-top: 15px;">
            <a href="registro.php" style="color: #a83232; font-size: 0.9rem; text-decoration: none; font-weight: 600;">¿no tenés cuenta? registrate acá</a>
        </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>