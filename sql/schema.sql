DROP DATABASE IF EXISTS TiendaPasteleria;
CREATE DATABASE TiendaPasteleria;

USE TiendaPasteleria;

CREATE TABLE   clientes(
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT  NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    codigo_postal VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol ENUM('CLIENTE', 'ADMIN') DEFAULT 'CLIENTE',
    empresa VARCHAR(100),
    nif VARCHAR(100),
  --  metodo_entrega ENUM ("domicilio","tienda")NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);
        

 CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria_principal ENUM('BOCADITOS', 'POSTRES', 'PERSONALES') NOT NULL,
    subcategoria ENUM('salados', 'dulces', 'postres', 'tartas') NOT NULL,
    tipo_precio ENUM('unitario','mediano','grande') NOT NULL DEFAULT 'unitario',
    -- Campos para tamaños y raciones
     
    precio_mediano DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    raciones_mediano VARCHAR(15) DEFAULT '8-12 porciones',
    precio_grande DECIMAL(10,2) NOT NULL DEFAULT 0.00;
    raciones_grande VARCHAR(15) DEFAULT '16-20 porciones',
    -- Para productos sin tamaños (bocaditos)
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- Usar cuando no hay tamaños
    
    imagen VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE, -- para pausar productos temporalmente
    destacado BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    precio_total DECIMAL(10,2),
    fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE,
    direccion_entrega VARCHAR(255) NOT NULL, -- Nuevo campo para dirección de entrega
    metodo_pago ENUM('Tarjeta', 'Paypal', 'Transferencia') NOT NULL,
    
    estado_pago ENUM('Pendiente', 'Pagado', 'Fallido') DEFAULT 'Pendiente',
    estado_pedido ENUM('Pendiente', 'Preparación', 'Entregado') DEFAULT 'Pendiente',
    telefono VARCHAR(20),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);


CREATE TABLE pedidos_productos (
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL, 
    cantidad INT,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_pedido, id_producto),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);

CREATE USER usu_tienda IDENTIFIED BY "usu_tienda";
GRANT ALL ON TiendaPasteleria.* TO usu_tienda;


-- ALTER TABLE productos
-- ADD COLUMN tipo_precio ENUM('unitario','mediano','grande') NOT NULL DEFAULT 'unitario'
-- AFTER subcategoria;

--  ALTER TABLE productos
-- MODIFY COLUMN precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
-- MODIFY COLUMN precio_mediano DECIMAL(10,2) NOT NULL DEFAULT 0.00,
-- MODIFY COLUMN precio_grande DECIMAL(10,2) NOT NULL DEFAULT 0.00;
