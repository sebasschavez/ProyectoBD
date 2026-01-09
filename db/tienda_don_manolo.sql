SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `Detalle_Venta` (
  `ID_Venta` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Cantidad_vendida` int(11) NOT NULL,
  `Precio_en_Venta` decimal(10,2) NOT NULL
);



INSERT INTO `Detalle_Venta` (`ID_Venta`, `ID_Producto`, `Cantidad_vendida`, `Precio_en_Venta`) VALUES
(1, 1, 2, '25.50');


CREATE TABLE `Empleado` (
  `ID_Empleado` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Puesto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `Empleado` (`ID_Empleado`, `Nombre`, `Apellido`, `Puesto`) VALUES
(1, 'Manuel', 'García', 'Encargado'),
(2, 'Carlos', 'López', 'Cajero'),
(3, 'Ana', 'Martínez', 'Inventario');


CREATE TABLE `Producto` (
  `ID_Producto` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Cantidad_Stock` int(11) NOT NULL,
  `Tipo_Producto` varchar(50) NOT NULL,
  `Fecha_Caducidad` date DEFAULT NULL,
  `Requiere_Refrigeracion` tinyint(1) DEFAULT NULL,
  `Marca` varchar(100) DEFAULT NULL,
  `Contenido_Neto` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_Producto`)
);


INSERT INTO `Producto` (`ID_Producto`, `Nombre`, `Descripcion`, `Precio_Venta`, `Cantidad_Stock`, `Tipo_Producto`, `Fecha_Caducidad`, `Requiere_Refrigeracion`, `Marca`, `Contenido_Neto`) VALUES
(1, 'Leche Lala 1L', 'Leche entera pasteurizada', '25.50', 28, 'Perecedero', '2025-12-15', 1, NULL, NULL),
(2, 'Yogurt Natural', 'Yogurt natural sin azúcar', '18.00', 20, 'Perecedero', '2025-11-30', 1, NULL, NULL),
(3, 'Pan Blanco', 'Pan de caja blanco', '35.00', 15, 'Perecedero', '2025-11-20', 0, NULL, NULL),
(4, 'Coca Cola 600ml', 'Refresco de cola', '18.00', 50, 'Abarrote', NULL, NULL, 'Coca Cola', '600ml'),
(5, 'Arroz Blanco', 'Arroz grano largo', '32.00', 40, 'Abarrote', NULL, NULL, 'Verde Valle', '1kg'),
(6, 'Frijol Negro', 'Frijol negro de Zacatecas', '28.00', 35, 'Abarrote', NULL, NULL, 'Isadora', '900g'),
(7, 'Aceite Vegetal', 'Aceite de cocina', '45.00', 25, 'Abarrote', NULL, NULL, '123', '1L');


CREATE TABLE `Proveedor` (
  `ID_Proveedor` int(11) NOT NULL,
  `Nombre_Empresa` varchar(150) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `Proveedor` (`ID_Proveedor`, `Nombre_Empresa`, `Email`, `Telefono`) VALUES
(1, 'Distribuidora La Central', 'ventas@lacentral.com', '5512345678'),
(2, 'Abarrotes del Norte', 'contacto@abnorte.com', '5587654321'),
(3, 'Productos Lala', 'pedidos@lala.com', '5555555555');


CREATE TABLE `Suministra` (
  `ID_Proveedor` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Costo_de_Compra` decimal(10,2) DEFAULT NULL
) ;


INSERT INTO `Suministra` (`ID_Proveedor`, `ID_Producto`, `Costo_de_Compra`) VALUES
(1, 3, '28.00'),
(1, 4, '14.00'),
(1, 7, '35.00'),
(2, 5, '25.00'),
(2, 6, '22.00'),
(3, 1, '20.00'),
(3, 2, '14.00');


CREATE TABLE `Usuario` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `ID_Empleado` int(11) DEFAULT NULL,
  `Rol` enum('admin','vendedor') DEFAULT 'vendedor',
  `Activo` tinyint(1) DEFAULT 1,
  `Fecha_Creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `Usuario` (`ID_Usuario`, `Nombre_Usuario`, `Contrasena`, `ID_Empleado`, `Rol`, `Activo`, `Fecha_Creacion`) VALUES
(3, 'Sebas', '$2y$10$QPxHvn6L1KvM663LBj136eIMc2IemsD2zuoJac6WMJZQq4eeRE5.q', NULL, 'vendedor', 1, '2026-01-06 06:49:58');


CREATE TABLE `Venta` (
  `ID_Venta` int(11) NOT NULL AUTO_INCREMENT,
  `Venta_Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `Total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ID_Empleado` int(11) NOT NULL,
  PRIMARY KEY (`ID_Venta`)
);



INSERT INTO `Venta` (`ID_Venta`, `Venta_Fecha`, `Total`, `ID_Empleado`) VALUES
(1, '2026-01-06 07:00:42', '51.00', 1);

ALTER TABLE `Empleado`
  ADD PRIMARY KEY (`ID_Empleado`);


ALTER TABLE `Proveedor`
  ADD PRIMARY KEY (`ID_Proveedor`),
  ADD UNIQUE KEY `Email` (`Email`);


ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Nombre_Usuario` (`Nombre_Usuario`),
  ADD KEY `ID_Empleado` (`ID_Empleado`);

ALTER TABLE `Empleado`
  MODIFY `ID_Empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Producto`
  MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `Proveedor`
  MODIFY `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Usuario`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Venta`
  MODIFY `ID_Venta` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
