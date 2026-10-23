<?php
function es_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function requerir_login() {
    if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'logeado') {
        header("location: /hotel2/views/auth/login.php");
        exit;
    }
}

function validar_campos_vacios($campos) {
    foreach ($campos as $campo) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            throw new exception("el campo \"$campo\" es obligatorio.");
        }
    }
}

function validar_formato_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new exception("el formato del correo no es válido.");
    }
}

function validar_ci($ci) {
    if (!is_numeric($ci) || (int)$ci <= 0) {
        throw new exception("la cédula tiene que ser un número válido.");
    }
}

function validar_tarjeta($tarjeta) {
    if (!is_numeric($tarjeta) || (int)$tarjeta <= 0) {
        throw new exception("el número de tarjeta tiene que ser un número válido.");
    }
}

function validar_fechas_reserva($fecha_in, $fecha_out) {
    $hoy = date('Y-m-d');
    
    if ($fecha_in < $hoy) {
        throw new Exception("la fecha de inicio no puede ser anterior a hoy.");
    }
    
    if ($fecha_out <= $fecha_in) {
        throw new Exception("la fecha de salida debe ser al menos un día posterior a la fecha de inicio.");
    }
}
function validar_texto_sin_numeros($valor, $nombre_campo) {
    if (preg_match('/[0-9]/', $valor)) {
        throw new Exception("el campo $nombre_campo no puede contener números.");
    }
}

function validar_telefono($telefono) {
    if (!preg_match('/^[0-9\s\+\-\(\)]+$/', $telefono)) {
        throw new Exception("el teléfono ingresado no es válido.");
    }
}

function validar_mayor_edad($fecha_nacimiento) {
    $hoy = new DateTime();
    $nacimiento = new DateTime($fecha_nacimiento);
    $edad = $hoy->diff($nacimiento)->y;
    
    if ($edad < 18) {
        throw new Exception("debés ser mayor de 18 años para registrarte.");
    }
}

function validar_vencimiento_tarjeta($vencimiento) {
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $vencimiento, $matches)) {
        throw new Exception("el formato del vencimiento debe ser MM/YY.");
    }
    
    $mes = (int)$matches[1];
    $anio = (int)("20" . $matches[2]);
    $anActual = (int)date('Y');
    $mesActual = (int)date('m');

    if ($anio < $anActual || ($anio === $anActual && $mes < $mesActual)) {
        throw new Exception("la tarjeta está vencida.");
    }
}

function validar_cvv($cvv) {
    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        throw new Exception("el CVV debe ser de 3 o 4 dígitos y numéricos.");
    }
}

function validar_cardholder($cardholder) {
    if (preg_match('/[0-9]/', $cardholder) || !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/', $cardholder)) {
        throw new Exception("el cardholder solo puede contener letras.");
    }
}