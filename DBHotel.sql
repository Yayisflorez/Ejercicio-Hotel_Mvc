# Base de Datos MySQL – Hotel Viña del Mar

CREATE DATABASE hotel;
USE hotel;

-- =========================
-- TABLA TIPOS DE DOCUMENTO
-- =========================
CREATE TABLE tipos_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL
);

-- =========================
-- INSERTAR TIPOS DE DOCUMENTO
-- =========================
INSERT INTO tipos_documento (tipo) 
VALUES ('Cédula de Ciudadanía'), 
('Cédula de Extranjería'), 
('Pasaporte');


-- =========================
-- TABLA ROLES
-- =========================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

-- =========================
-- INSERT ROLES
-- =========================
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Control total del sistema y gestión general'),
('Cliente', 'Usuario que realiza reservas y pagos'),
('Recepcionista', 'Encargado de gestionar reservas y atención al cliente'),
('Empleado', 'Personal del hotel con acceso limitado al sistema');

-- =========================
-- TABLA ESTADOS
-- =========================
CREATE TABLE estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

-- =========================
-- INSERT ESTADOS
-- =========================
INSERT INTO estados (nombre, descripcion) VALUES
('Disponible', 'Elemento disponible para uso o reserva'),
('Ocupado', 'Actualmente en uso o reservado'),
('Pendiente', 'Proceso pendiente de confirmación'),
('Cancelado', 'Registro cancelado por el usuario o sistema'),
('Activo', 'Usuario o elemento activo en el sistema'),
('Inactivo', 'Usuario o elemento deshabilitado'),
('Mantenimiento', 'Habitación fuera de servicio por mantenimiento'),
('Finalizado', 'Reserva completada exitosamente');

-- =========================
-- TABLA USUARIOS
-- =========================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30),
    tipo_documento_id INT,
    documento VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_rol INT,
    estado INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento(id),
    FOREIGN KEY (id_rol) REFERENCES roles(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- =========================
-- TABLA CATEGORIAS
-- =========================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- =========================
-- INSERT CATEGORIAS
-- =========================
INSERT INTO categorias (nombre, descripcion) VALUES
('Estándar', 'Habitación cómoda y funcional para una estadía sencilla'),
('Superior', 'Habitación con mayor espacio y mejores comodidades'),
('Deluxe', 'Habitación de lujo con acabados premium y vista privilegiada'),
('Familiar', 'Habitación amplia ideal para grupos o familias');

-- =========================
-- TABLA HABITACIONES
-- =========================
CREATE TABLE habitaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    num_habitacion VARCHAR(20) NOT NULL UNIQUE,
    id_categoria INT,
    num_camas INT NOT NULL,
    max_personas INT NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    estado INT,

    FOREIGN KEY (id_categoria) REFERENCES categorias(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- =========================
-- TABLA METODOS DE PAGO
-- =========================
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- =========================
-- INSERT METODOS DE PAGO
-- =========================
INSERT INTO metodos_pago (nombre, descripcion) VALUES
('Nequi', 'Pago mediante billetera digital Nequi'),
('Daviplata', 'Pago mediante billetera digital Daviplata'),
('Bancolombia', 'Pago por transferencia o cuenta Bancolombia');

-- =========================
-- TABLA RESERVAS
-- =========================
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_habitacion INT,
    fecha_inicio DATE NOT NULL,
    fecha_final DATE NOT NULL,
    num_personas INT NOT NULL,
    estado INT,
    precio DECIMAL(10,2),
    id_metodo_pago INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_user) REFERENCES usuarios(id),
    FOREIGN KEY (id_habitacion) REFERENCES habitaciones(id),
    FOREIGN KEY (estado) REFERENCES estados(id),
    FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago(id)
);