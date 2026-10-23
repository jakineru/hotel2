use hotel_db;

-- ejemplo1: detalle completo de reservas con join usuarios/habitaciones
CREATE VIEW vista_reservas_detalladas as
SELECT 
    r.id as id_reserva,
    u.ci,
    concat(u.nombres, ' ', u.apellidos) as cliente,
    r.numero_hab,
    h.tipo as tipo_habitacion,
    r.fecha_in,
    r.fecha_out,
    r.estado_pago
FROM reservas r
JOIN usuarios u ON r.ci_usuario = u.ci
JOIN habitaciones h ON r.numero_hab = h.numero;

-- ejemplo dos: habitaciones disponbiles
CREATE VIEW vista_habitaciones_disponibles as
SELECT 
    numero,
    tipo,
    precio,
    capacidad,
    descripcion
FROM habitaciones
WHERE estado = 'disponible';

-- ejemplo 3: resumen en grupo de reservas e ingresos teorico
CREATE VIEW vista_ingresos_por_tipo as
SELECT 
    h.tipo,
    count(r.id) as total_reservas,
    sum(h.precio) as total_recaudado
FROM habitaciones h
LEFT JOIN reservas r on h.numero = r.numero_hab
GROUP BY h.tipo;

-- PARA TESTEARLOS:

select * from vista_reservas_detalladas;
select * from vista_habitaciones_disponibles;
select * from vista_ingresos_por_tipo;
