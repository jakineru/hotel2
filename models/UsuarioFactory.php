<?php
abstract class UsuarioBase {
    protected $ci, $nombres, $apellidos, $email, $contra, $estado, $rol;

    public function __construct($datos) {
        $this->ci = $datos['ci'];
        $this->nombres = $datos['nombres'];
        $this->apellidos = $datos['apellidos'];
        $this->email = $datos['email'];
        $this->contra = $datos['contra'];
        $this->estado = $datos['estado'] ?? 'activo';
    }

    public function getCi() { return $this->ci; }
    public function getNombres() { return $this->nombres; }
    public function getEmail() { return $this->email; }
    public function getRol() { return $this->rol; }
    public function getContra() { return $this->contra; }

    public function getEstado() { return $this->estado; }
}

class Administrador extends UsuarioBase {
    protected $rol = 'admin';
    private $permisosTotales = true;

    public function tienePermisosTotales() { return $this->permisosTotales; }
}

class Cliente extends UsuarioBase {
    protected $rol = 'cliente';
    private $fechaNacimiento, $telefono;

    public function __construct($datos) {
        parent::__construct($datos);
        $this->fechaNacimiento = $datos['fecha_nacimiento'] ?? null;
        $this->telefono = $datos['telefono'] ?? '';
    }

    public function getTelefono() { return $this->telefono; }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
}

class UsuarioFactory {
    public static function crearUsuario(string $rol, array $datos) {
        $rol = strtolower(trim($rol));
        
        switch ($rol) {
            case 'admin':
                return new Administrador($datos);
            case 'cliente':
            default:
                return new Cliente($datos);
        }
    }
}