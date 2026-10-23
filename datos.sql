USE hotel_db;

INSERT INTO usuarios (ci, nombres, apellidos, email, contra, rol, estado) values 
('admin', 'admin', 'admin', 'admin@skibidihotel.com.uy', 'admin', 'admin', 'activo'),
('demo', 'juan', 'perez', 'demo@skibidihotel.com.uy', 'demo', 'cliente', 'activo');

INSERT INTO habitaciones (numero, tipo, precio, capacidad, descripcion, estado, imagen) values
(101, 'simple', 1350, 1, 'habitación básica para una persona.', 'disponible', '/hotel2/img/simple.jpg'),
(102, 'doble', 2140, 2, 'opción para parejas.', 'disponible', '/hotel2/img/doble.webp'),
(201, 'suite', 5600, 4, 'suite premium con vista al mar.', 'disponible', '/hotel2/img/suite.jpg'),
(202, 'doble', 2100, 2, 'habitación en mantenimiento.', 'mantenimiento', '/hotel2/img/mantenimiento.webp');