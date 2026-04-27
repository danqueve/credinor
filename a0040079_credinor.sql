-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-04-2026 a las 18:57:36
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `a0040079_credinor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `sucursal_id` int UNSIGNED NOT NULL,
  `vendedor_id` int UNSIGNED NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_clientes_dni` (`dni`),
  KEY `fk_clientes_vendedor` (`vendedor_id`),
  KEY `idx_clientes_sucursal` (`sucursal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `sucursal_id`, `vendedor_id`, `dni`, `nombre`, `telefono`, `email`, `domicilio`, `localidad`, `lat`, `lng`, `observaciones`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '44616196', 'Abdala Andole Leila Anahi', '3812175253', NULL, 'Pje Saenz Peña 3841', 'Tucuman', NULL, NULL, '', 1, '2026-04-27 15:18:29', '2026-04-27 15:18:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`clave`, `valor`, `descripcion`, `updated_at`) VALUES
('moneda_codigo', 'ARS', 'Código de moneda ISO', '2026-04-27 14:25:37'),
('moneda_simbolo', '$', 'Símbolo de moneda', '2026-04-27 14:25:37'),
('porcentaje_mora_diaria_default', '0.1000', 'Porcentaje de mora diaria por defecto (0.10 = 0.10%)', '2026-04-27 14:25:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditos`
--

DROP TABLE IF EXISTS `creditos`;
CREATE TABLE IF NOT EXISTS `creditos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `sucursal_id` int UNSIGNED NOT NULL,
  `cliente_id` int UNSIGNED NOT NULL,
  `vendedor_id` int UNSIGNED NOT NULL,
  `cobrador_id` int UNSIGNED DEFAULT NULL,
  `garante_id` int UNSIGNED DEFAULT NULL,
  `monto_prestado` decimal(12,2) NOT NULL,
  `monto_a_devolver` decimal(12,2) NOT NULL,
  `cantidad_cuotas` smallint UNSIGNED NOT NULL,
  `frecuencia` enum('diaria','semanal','quincenal','mensual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_primera_cuota` date NOT NULL,
  `aplica_mora` tinyint(1) NOT NULL DEFAULT '0',
  `porcentaje_mora_diaria` decimal(5,4) DEFAULT NULL,
  `mora_acumulada` decimal(12,2) NOT NULL DEFAULT '0.00',
  `mora_pagada` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado` enum('pendiente_autorizacion','activo','finalizado','rechazado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente_autorizacion',
  `motivo_rechazo` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_creditos_vendedor` (`vendedor_id`),
  KEY `fk_creditos_garante` (`garante_id`),
  KEY `idx_creditos_estado` (`estado`),
  KEY `idx_creditos_cobrador` (`cobrador_id`),
  KEY `idx_creditos_cliente` (`cliente_id`),
  KEY `idx_creditos_sucursal` (`sucursal_id`)
) ;

--
-- Volcado de datos para la tabla `creditos`
--

INSERT INTO `creditos` (`id`, `sucursal_id`, `cliente_id`, `vendedor_id`, `cobrador_id`, `garante_id`, `monto_prestado`, `monto_a_devolver`, `cantidad_cuotas`, `frecuencia`, `fecha_inicio`, `fecha_primera_cuota`, `aplica_mora`, `porcentaje_mora_diaria`, `mora_acumulada`, `mora_pagada`, `estado`, `motivo_rechazo`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 3, NULL, 50000.00, 100000.00, 10, 'diaria', '2026-04-27', '2026-05-28', 0, NULL, 0.00, 0.00, 'activo', NULL, '', '2026-04-27 15:32:41', '2026-04-27 15:32:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditos_log`
--

DROP TABLE IF EXISTS `creditos_log`;
CREATE TABLE IF NOT EXISTS `creditos_log` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `credito_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `estado_desde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_hasta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_log_usuario` (`usuario_id`),
  KEY `idx_log_credito` (`credito_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `creditos_log`
--

INSERT INTO `creditos_log` (`id`, `credito_id`, `usuario_id`, `estado_desde`, `estado_hasta`, `nota`, `created_at`) VALUES
(1, 1, 3, NULL, 'activo', 'Crédito creado y activado por staff.', '2026-04-27 15:32:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuotas`
--

DROP TABLE IF EXISTS `cuotas`;
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `credito_id` int UNSIGNED NOT NULL,
  `numero_cuota` smallint UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('pendiente','parcial','pagada','vencida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cuota_numero` (`credito_id`,`numero_cuota`),
  KEY `idx_cuotas_fecha` (`fecha_vencimiento`),
  KEY `idx_cuotas_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuotas`
--

INSERT INTO `cuotas` (`id`, `credito_id`, `numero_cuota`, `monto`, `fecha_vencimiento`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10000.00, '2026-05-28', 'pagada', '2026-04-27 15:32:41', '2026-04-27 15:33:44'),
(2, 1, 2, 10000.00, '2026-05-29', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(3, 1, 3, 10000.00, '2026-05-30', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(4, 1, 4, 10000.00, '2026-05-31', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(5, 1, 5, 10000.00, '2026-06-01', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(6, 1, 6, 10000.00, '2026-06-02', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(7, 1, 7, 10000.00, '2026-06-03', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(8, 1, 8, 10000.00, '2026-06-04', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(9, 1, 9, 10000.00, '2026-06-05', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41'),
(10, 1, 10, 10000.00, '2026-06-06', 'pendiente', '2026-04-27 15:32:41', '2026-04-27 15:32:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `garantes`
--

DROP TABLE IF EXISTS `garantes`;
CREATE TABLE IF NOT EXISTS `garantes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` int UNSIGNED DEFAULT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_garantes_cliente` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mora_devengada`
--

DROP TABLE IF EXISTS `mora_devengada`;
CREATE TABLE IF NOT EXISTS `mora_devengada` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cuota_id` int UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `saldo_base` decimal(12,2) NOT NULL,
  `porcentaje` decimal(6,4) NOT NULL,
  `monto_mora` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mora_cuota_fecha` (`cuota_id`,`fecha`),
  KEY `idx_mora_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cuota_id` int UNSIGNED NOT NULL,
  `cobrador_id` int UNSIGNED NOT NULL,
  `rendicion_id` int UNSIGNED DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `monto_a_capital` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_a_mora` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` enum('efectivo','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `estado` enum('pendiente_rendir','rendido','confirmado','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente_rendir',
  `anulado_at` datetime DEFAULT NULL,
  `anulado_por` int UNSIGNED DEFAULT NULL,
  `motivo_anulacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pagos_cuota` (`cuota_id`),
  KEY `idx_pagos_cobrador` (`cobrador_id`),
  KEY `idx_pagos_rendicion` (`rendicion_id`),
  KEY `idx_pagos_estado` (`estado`)
) ;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `cuota_id`, `cobrador_id`, `rendicion_id`, `monto`, `monto_a_capital`, `monto_a_mora`, `metodo_pago`, `estado`, `anulado_at`, `anulado_por`, `motivo_anulacion`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, 10000.00, 10000.00, 0.00, 'transferencia', 'pendiente_rendir', NULL, NULL, NULL, NULL, '2026-04-27 15:33:44', '2026-04-27 15:33:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rendiciones`
--

DROP TABLE IF EXISTS `rendiciones`;
CREATE TABLE IF NOT EXISTS `rendiciones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cobrador_id` int UNSIGNED NOT NULL,
  `sucursal_id` int UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `monto_declarado` decimal(12,2) NOT NULL,
  `monto_recibido` decimal(12,2) DEFAULT NULL,
  `diferencia` decimal(12,2) GENERATED ALWAYS AS ((`monto_recibido` - `monto_declarado`)) STORED,
  `estado` enum('pendiente','confirmada','rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `admin_id` int UNSIGNED DEFAULT NULL,
  `confirmado_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rendicion_dia` (`cobrador_id`,`fecha`),
  KEY `fk_rendiciones_sucursal` (`sucursal_id`),
  KEY `fk_rendiciones_admin` (`admin_id`),
  KEY `idx_rendicion_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `activa`, `created_at`, `updated_at`) VALUES
(1, 'Casa Central', 'Av. Principal 123', '299-4000000', 1, '2026-04-27 14:25:37', '2026-04-27 14:25:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `sucursal_id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','vendedor','cobrador') COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_username` (`username`),
  KEY `idx_usuarios_rol` (`rol`),
  KEY `idx_usuarios_sucursal` (`sucursal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `sucursal_id`, `username`, `nombre`, `password`, `rol`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'Administrador', '$2y$12$YQHrT45XCeaeUjmgaHUI1ezhI08tYbnfntYNijChdBVYr7qZLx596', 'admin', 1, '2026-04-27 14:25:37', '2026-04-27 14:25:37'),
(2, 1, 'danqueve', 'Alejandro Quevedo', '$2y$12$nJ2axzH7.3Fx48rgCwxAXugjo1Ys5J75mFRIf9PPlJEDGKy4sm/Oy', 'admin', 1, '2026-04-27 14:43:32', '2026-04-27 14:43:32'),
(3, 1, 'ventas_t', 'Ventas', '$2y$12$D0d/9c5/suD.Nf.BON2wse4bW0wB1s/DwqrLxKMwrGVG.kZhtq.EW', 'vendedor', 1, '2026-04-27 15:17:34', '2026-04-27 15:17:34');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  ADD CONSTRAINT `fk_clientes_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `creditos`
--
ALTER TABLE `creditos`
  ADD CONSTRAINT `fk_creditos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_creditos_cobrador` FOREIGN KEY (`cobrador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_creditos_garante` FOREIGN KEY (`garante_id`) REFERENCES `garantes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_creditos_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  ADD CONSTRAINT `fk_creditos_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `creditos_log`
--
ALTER TABLE `creditos_log`
  ADD CONSTRAINT `fk_log_credito` FOREIGN KEY (`credito_id`) REFERENCES `creditos` (`id`),
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cuotas`
--
ALTER TABLE `cuotas`
  ADD CONSTRAINT `fk_cuotas_credito` FOREIGN KEY (`credito_id`) REFERENCES `creditos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `garantes`
--
ALTER TABLE `garantes`
  ADD CONSTRAINT `fk_garantes_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mora_devengada`
--
ALTER TABLE `mora_devengada`
  ADD CONSTRAINT `fk_mora_cuota` FOREIGN KEY (`cuota_id`) REFERENCES `cuotas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_cobrador` FOREIGN KEY (`cobrador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_pagos_cuota` FOREIGN KEY (`cuota_id`) REFERENCES `cuotas` (`id`),
  ADD CONSTRAINT `fk_pagos_rendicion` FOREIGN KEY (`rendicion_id`) REFERENCES `rendiciones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `rendiciones`
--
ALTER TABLE `rendiciones`
  ADD CONSTRAINT `fk_rendiciones_admin` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_rendiciones_cobrador` FOREIGN KEY (`cobrador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_rendiciones_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
