USE hotel_db;

-- 1. habitaciones disponibles que salgan menos de 3000
select * from habitaciones where estado = 'disponible' and precio < 3000;

-- 2. listar reservas cruzando datos de usuarios y habitaciones
select r.id, u.nombres, u.apellidos, r.numero_hab, r.fecha_in 
from reservas r 
join usuarios u on r.ci_usuario = u.ci 
join habitaciones h on r.numero_hab = h.numero;

-- 3. contar el total de reservas registradas
select count(*) as total_reservas from reservas;

-- 4. calcular la suma de los precios de todas las habitaciones
select sum(precio) as suma_total_precios from habitaciones;

-- 5. calcular el precio promedio de las habitaciones 
select avg(precio) as precio_promedio from habitaciones;

-- 6. ver el precio maximo de las habitaciones
select max(precio) as precio_maximo from habitaciones;

-- 7. ver el precio minimo de las habitaciones
select min(precio) as precio_minimo from habitaciones;

-- 8. agrupar y contar la cantidad de habitaciones por su tipo
select tipo, count(*) as cantidad_por_tipo from habitaciones group by tipo;

-- 9. listar habitaciones ordenadas por precio de mayor a menor
select * from habitaciones order by precio desc;

-- 10. ingresos potenciales agrupados segun el tipo de la habitacion
select h.tipo, count(r.id) as total_reservas, sum(h.precio) as recaudacion_total 
from habitaciones h 
left join reservas r on h.numero = r.numero_hab 
group by h.tipo 
order by recaudacion_total desc;