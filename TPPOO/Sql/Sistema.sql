CREATE TABLE Cliente (
    id AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE Mascota (
    id VARCHAR(255) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    raza VARCHAR(50) NOT NULL,
    clienteId INT,
    historialMedico TEXT,
    FOREIGN KEY (clienteId) REFERENCES Cliente(id) ON DELETE CASCADE
);

CREATE TABLE Producto (
    codigo INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10.2) NOT NULL,
    stock INT NOT NULL
);

CREATE TABLE Veterinaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(15) NOT NULL
);

CREATE TABLE Veterinario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL
)