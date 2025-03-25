CREATE TABLE Cliente (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    dni int(11) NOT NULL UNIQUE
);

CREATE TABLE Mascota (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    edad INT(11) NOT NULL,
    raza VARCHAR(50) NOT NULL,
    historialMedico TEXT,
    clienteId INT(11),
    FOREIGN KEY (clienteId) REFERENCES Cliente(id) ON DELETE CASCADE
);

CREATE TABLE Producto (
    id INT(11) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10.2) NOT NULL,
    stock INT(50) NOT NULL
);

CREATE TABLE Inventario (
    clienteId INT(11),
    productoId INT(11),
    cantidad INT NOT NULL DEFAULT 0,
    PRIMARY KEY (clienteId, productoId),
    FOREIGN KEY (clienteId) REFERENCES Cliente(id) ON DELETE CASCADE,
    FOREIGN KEY (productoId) REFERENCES Producto(id) ON DELETE CASCADE
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