<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/UsuarioFactory.php'; // <--- IMPORTANTE: traer la fábrica

class Usuario {
    public static function obtenerPorCi($ci) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE ci = ?");
        $stmt->execute([$ci]);
        $data = $stmt->fetch();

        if (!$data) return null;

        // uso del factory para convertirlo en un objeto de poo 
        return UsuarioFactory::crearUsuario($data['tipo'], $data);
    }

    public static function obtenerPorEmail($email) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return UsuarioFactory::crearUsuario($data['tipo'], $data);
    }

    public static function crear($ci, $nombres, $apellidos, $fecha_nacimiento, $telefono, $email, $contra) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("INSERT INTO usuarios (ci, nombres, apellidos, fecha_nacimiento, telefono, email, contra, estado, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo', 'cliente')");
        return $stmt->execute([$ci, $nombres, $apellidos, $fecha_nacimiento, $telefono, $email, $contra]);
    }
}