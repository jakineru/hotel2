<?php
class UsuarioFactory {
    public static function crearUsuario(string $rol, array $datos) {
        $rol = strtolower(trim($rol));
        
        switch ($rol) {
            case 'admin':
                return [
                    'ci' => $datos['ci'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'email' => $datos['email'],
                    'contra' => $datos['contra'],
                    'rol' => 'admin',
                    'estado' => $datos['estado'] ?? 'activo',
                    'permisos_totales' => true
                ];
                
            case 'cliente':
            default:
                return [
                    'ci' => $datos['ci'],
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                    'telefono' => $datos['telefono'] ?? '',
                    'email' => $datos['email'],
                    'contra' => $datos['contra'],
                    'rol' => 'cliente',
                    'estado' => 'activo'
                ];
        }
    }
}