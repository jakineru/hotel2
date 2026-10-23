<?php
require_once __DIR__ . '/../config/Conexion.php';

class Reserva {
    public static function crear($ci_usuario, $numero_hab, $fecha_in, $fecha_out) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $id = 'RES-' . uniqid();
        $stmt = $db->prepare("INSERT INTO reservas (id, ci_usuario, numero_hab, fecha_in, fecha_out, estado_pago) VALUES (?, ?, ?, ?, ?, 'pendiente')");
        return $stmt->execute([$id, $ci_usuario, $numero_hab, $fecha_in, $fecha_out]);
    }

    public static function obtenerPorUsuario($ci_usuario) {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->prepare("SELECT r.*, h.tipo, h.precio FROM reservas r JOIN habitaciones h ON r.numero_hab = h.numero WHERE r.ci_usuario = ?");
        $stmt->execute([$ci_usuario]);
        return $stmt->fetchAll();
    }
}