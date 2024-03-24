-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-02-2024 a las 14:22:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `codigo` varchar(8) CHARACTER SET utf8 NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8 NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8 NOT NULL,
  `categoria` int(11) NOT NULL,
  `precio` float(10,2) NOT NULL,
  `imagen` varchar(100) CHARACTER SET utf8 NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`, `activo`) VALUES
('aaa11111', 'RTX 4060', 'Tarjeta grafica gaming', 3, 320.00, 'Descargas/65d858ff95c6a.jpg', 1),
('bbb22222', 'RTX 4060 Ti', 'Tarjeta grafica gaming mas potente', 3, 500.00, 'Descargas/65d8cbbac4197.jpg', 1),
('ccc33333', 'NGS Evo Karma', 'Raton gaming', 4, 33.00, 'Descargas/65732031a55ae.jpg', 1),
('ddd44444', 'Corsair K60 RGB', 'Teclado gaming', 5, 78.00, 'Descargas/657320645f9ed.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `id_super` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `id_super`, `activo`) VALUES
(1, 'Hardware', 0, 1),
(2, 'Perifericos', 0, 1),
(3, 'Tarjetas Graficas', 1, 1),
(4, 'Ratones', 2, 1),
(5, 'Teclados', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `dni` varchar(9) CHARACTER SET utf8 NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8 NOT NULL,
  `direccion` varchar(50) CHARACTER SET utf8 NOT NULL,
  `localidad` varchar(30) CHARACTER SET utf8 NOT NULL,
  `provincia` varchar(30) CHARACTER SET utf8 NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8 NOT NULL,
  `email` varchar(30) CHARACTER SET utf8 NOT NULL,
  `contrasenya` varchar(500) CHARACTER SET utf8 NOT NULL,
  `rol` varchar(30) CHARACTER SET utf8 NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`dni`, `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `contrasenya`, `rol`, `activo`) VALUES
('74363057D', 'Elena', 'c primera', 'Elche', 'Alicante', '666555111', 'elena@prueba.com', '$2y$10$xILvOClYLNOPTC4u5hEcsuV.MUMEZCQF3QDVNSlbH9cZ.JfEzgaxa', 'administrador', 1),
('74443616E', 'Mel', 'C Segunda', 'Elche', 'Alicante', '666555222', 'mel@prueba.com', '$2y$10$pAkk0dD3opUD.e83i.RSdOJ8hOALbK2DLGY22ANdwpiP3Q5bH3DQy', 'editor', 1),
('21980592K', 'Paco', 'C Tercera', 'Elche', 'Alicante', '666555333', 'paco@prueba.com', '$2y$10$i9COKHi0KXVzADYbgHEefOnSeDCDAJcRrXNQArdTBn0XQ4qa4V9P.', 'usuario', 1),
('21974763B', 'Paca', 'C Cuarta', 'Elche', 'Alicante', '666555444', 'paca@prueba.com', '$2y$10$qL.RbsY9rKjoySxdF/TG5uUbB4LgKL2RdqOV2g/an/pvnLjIt9296', 'usuario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL,
  `idCliente` varchar(9) CHARACTER SET utf8 NOT NULL,
  `total` float(10,2) NOT NULL,
  `fCreacion` datetime NOT NULL,
  `estado` smallint(6) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`idPedido`, `idCliente`, `total`, `fCreacion`, `estado`, `activo`) VALUES
(62, '21980592K', 78.00, '2024-01-03 22:23:14', 0, 1),
(63, '74363057D', 189.00, '2024-01-03 23:14:15', 1, 1),
(64, '74443616E', 1000.00, '2024-02-23 14:35:16', 2, 1),
(65, '21974763B', 1140.00, '2024-02-23 14:36:54', 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_articulos`
--

CREATE TABLE `pedido_articulos` (
  `idPedArt` int(11) NOT NULL,
  `pedidoId` int(11) NOT NULL,
  `productoId` varchar(8) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pedido_articulos`
--

INSERT INTO `pedido_articulos` (`idPedArt`, `pedidoId`, `productoId`, `cantidad`) VALUES
(1, 62, 'ddd44444', 1),
(2, 63, 'ccc33333', 1),
(3, 63, 'ddd44444', 2),
(4, 64, 'bbb22222', 2),
(5, 65, 'aaa11111', 2),
(6, 65, 'bbb22222', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `FK_articulos` (`categoria`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`dni`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `FK_pedido` (`idCliente`);

--
-- Indices de la tabla `pedido_articulos`
--
ALTER TABLE `pedido_articulos`
  ADD PRIMARY KEY (`idPedArt`),
  ADD KEY `FK_parta` (`pedidoId`),
  ADD KEY `FK_partb` (`productoId`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `pedido_articulos`
--
ALTER TABLE `pedido_articulos`
  MODIFY `idPedArt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;