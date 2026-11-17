<?php
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/../models/UsuarioFactory.php';
require_once __DIR__ . '/../models/Usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/hotel2/';