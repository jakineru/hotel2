CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

CREATE TABLE usuarios (
    ci VARCHAR(20) PRIMARY KEY,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contra VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_nacimiento DATE NULL
);

CREATE TABLE habitaciones (
    numero INT PRIMARY KEY,
    tipo ENUM('simple', 'doble', 'suite') NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    capacidad INT NOT NULL,
    descripcion TEXT,
    estado ENUM('disponible', 'mantenimiento', 'inactiva') NOT NULL DEFAULT 'disponible',
    imagen VARCHAR(255)
);

CREATE TABLE reservas (
    id VARCHAR(30) PRIMARY KEY,
    ci_usuario VARCHAR(20) NOT NULL,
    numero_hab INT NOT NULL,
    fecha_in DATE NOT NULL,
    fecha_out DATE NOT NULL,
    estado_pago ENUM('pendiente', 'pagado') NOT NULL DEFAULT 'pendiente',
    FOREIGN KEY (ci_usuario) REFERENCES usuarios(ci),
    FOREIGN KEY (numero_hab) REFERENCES habitaciones(numero)
);

CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_reserva VARCHAR(30) NOT NULL,
    nro_tarjeta VARCHAR(20) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_reserva) REFERENCES reservas(id)
);