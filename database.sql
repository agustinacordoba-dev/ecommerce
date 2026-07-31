CREATE DATABASE IF NOT EXISTS DHImport;
USE DHImport;

-- Tabla Administrador
CREATE TABLE IF NOT EXISTS admin (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     nombre VARCHAR(100) NOT NULL,
                                     apellido VARCHAR(100) NOT NULL,
                                     email VARCHAR(150) NOT NULL UNIQUE,
                                     password VARCHAR(255) NOT NULL
);

-- Tabla Cliente (con autenticación)
CREATE TABLE IF NOT EXISTS cliente (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       codigoCliente VARCHAR(50) NOT NULL UNIQUE,
                                       nombre VARCHAR(100) NOT NULL,
                                       apellido VARCHAR(100) NOT NULL,
                                       email VARCHAR(150) NOT NULL UNIQUE,
                                       password VARCHAR(255) NOT NULL,
                                       telefono VARCHAR(30)
);

-- Tabla Categoría
CREATE TABLE IF NOT EXISTS categoria (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         nombre VARCHAR(100) NOT NULL
);

-- Tabla Producto
CREATE TABLE IF NOT EXISTS producto (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        codigo VARCHAR(50) NOT NULL UNIQUE,
                                        nombre VARCHAR(150) NOT NULL,
                                        descripcion TEXT,
                                        precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                                        stock INT NOT NULL DEFAULT 0,
                                        foto VARCHAR(255),
                                        categoria_id INT,

                                        CONSTRAINT fk_producto_categoria
                                            FOREIGN KEY (categoria_id)
                                                REFERENCES categoria(id)
                                                ON DELETE SET NULL
                                                ON UPDATE CASCADE
);

-- ---------------------- INSERT ----------------------
INSERT INTO categoria (nombre) VALUES
                                   ('Electrodomésticos'),
                                   ('Tecnología'),
                                   ('Cuidado Personal / Belleza'),
                                   ('Librería'),
                                   ('Hogar'),
                                   ('Ferretería');

INSERT INTO producto (codigo, nombre, descripcion, precio, stock, foto, categoria_id) VALUES
                                                                                          (
                                                                                              '083921',
                                                                                              'MOUSE GAMER RGB',
                                                                                              'Mouse óptico ergonómico con iluminación RGB personalizable, 6 botones programables y DPI ajustable.',
                                                                                              24649.00,
                                                                                              30,
                                                                                              'public/img/prod-1.jpg',
                                                                                              2  -- Tecnología
                                                                                          ),
                                                                                          (
                                                                                              '109245',
                                                                                              'ALMOHADA ESTANDAR 60 X 40',
                                                                                              'Almohada con relleno de fibra siliconada de alta densidad, funda lavable de algodón y tratamiento antiácaros.',
                                                                                              11200.00,
                                                                                              50,
                                                                                              'public/img/prod-2.jpg',
                                                                                              5  -- Hogar
                                                                                          ),
                                                                                          (
                                                                                              '335512',
                                                                                              'CONJUNTO DEPORTIVO',
                                                                                              'Conjunto deportivo de campera y pantalón en tela elastizada de secado rápido, ideal para entrenamiento.',
                                                                                              19500.00,
                                                                                              15,
                                                                                              'public/img/prod-3.jpg',
                                                                                              3  -- Indumentaria
                                                                                          ),
                                                                                          (
                                                                                              '447189',
                                                                                              'PROCESADORA 2 EN 1 JARRA DE VIDRIO 1000W',
                                                                                              'Procesadora y licuadora con motor potente de 1000W, jarra de vidrio resistente de 1.5L y cuchillas de acero inoxidable.',
                                                                                              9800.00,
                                                                                              40,
                                                                                              'public/img/prod-4.jpg',
                                                                                              1  -- Electrodomésticos
                                                                                          ),
                                                                                          (
                                                                                              '087364',
                                                                                              'PLANCHITA DE PELO',
                                                                                              'Planchita alisadora con placas de cerámica de rápido calentamiento, regulador de temperatura hasta 230°C y cable giratorio.',
                                                                                              11440.00,
                                                                                              20,
                                                                                              'public/img/prod-5.jpg',
                                                                                              3  -- Cuidado Personal y Belleza
                                                                                          ),
                                                                                          (
                                                                                              '562033',
                                                                                              'CABLE HDMI MALLADO 1.5M',
                                                                                              'Cable HDMI v2.0 ultra resistente con revestimiento mallado y conectores chapados en oro. Soporta resolución 4K y 60Hz.',
                                                                                              42990.00,
                                                                                              8,
                                                                                              'public/img/prod-6.jpg',
                                                                                              2  -- Tecnología
                                                                                          ),
                                                                                          (
                                                                                              '610348',
                                                                                              'REPRODUCTOR MP3 P/AUTO',
                                                                                              'Transmisor FM y reproductor MP3 para auto con conectividad Bluetooth, puertos USB de carga rápida y lector de tarjetas MicroSD.',
                                                                                              33750.00,
                                                                                              22,
                                                                                              'public/img/prod-7.jpg',
                                                                                              2  -- Tecnología
                                                                                          ),
                                                                                          (
                                                                                              '774519',
                                                                                              'ASPIRADORA LILIANA 1600W',
                                                                                              'Aspiradora liliana sin bolsa con filtro HEPA, 1600W de potencia, regulador de succión y depósito transparente lavable.',
                                                                                              16900.00,
                                                                                              12,
                                                                                              'public/img/prod-8.jpg',
                                                                                              1  -- Electrodomésticos (o Hogar)
                                                                                          );