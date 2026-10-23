<?php
require_once __DIR__ . '/../config/Conexion.php';

class Usuario {
    public static function obtenerPorCi($ci) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE ci = ?");
        $stmt->execute([$ci]);
        return $stmt->fetch();
    }

    public static function obtenerPorEmail($email) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function crear($ci, $nombres, $apellidos, $fecha_nacimiento, $telefono, $email, $contra) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("INSERT INTO usuarios (ci, nombres, apellidos, fecha_nacimiento, telefono, email, contra, estado, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo', 'cliente')");
        return $stmt->execute([$ci, $nombres, $apellidos, $fecha_nacimiento, $telefono, $email, $contra]);
    }
}