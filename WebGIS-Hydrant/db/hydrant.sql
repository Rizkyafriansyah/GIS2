CREATE DATABASE IF NOT EXISTS hydrant_db;
USE hydrant_db;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

INSERT INTO admin (username, password) VALUES
('admin', MD5('admin123'));

CREATE TABLE hydrant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    latitude DOUBLE,
    longitude DOUBLE,
    status VARCHAR(50)
);

INSERT INTO hydrant (nama, latitude, longitude, status) VALUES
('Hydrant 001', 3.5897, 98.6734, 'Layak'),
('Hydrant 002', 3.5982, 98.6759, 'Tidak Layak'),
('Hydrant 003', 3.5924, 98.6689, 'Layak');
