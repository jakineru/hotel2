USE hotel_db;

CREATE USER 'admin'@'localhost' identified by 'admin123';
GRANT all privileges on hotel_db.* to 'admin'@'localhost';

CREATE USER 'app_user'@'localhost' identified by 'app123';
GRANT select, insert, update, delete on hotel_db.* to 'app_user'@'localhost';

CREATE USER 'analyst'@'localhost' identified by 'analyst123';
GRANT select on hotel_db.* to 'analyst'@'localhost';

flush privileges;