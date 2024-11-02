-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2024 a las 19:39:32
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `casino`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `accion` varchar(10) NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `id_registro` int(11) NOT NULL,
  `detalle` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id_auditoria`, `accion`, `tabla`, `id_registro`, `detalle`, `fecha`) VALUES
(1, 'DELETE', 'jugador', 40, 'Jugador eliminado: Nombre: Alexander, Apellidos: Valdivia, DNI: Y9228879M, Edad: 22, Sexo: masculino, Apodo: Alexinzler, Saldo: 100, Email: pruebasalexander05@gmail.com, Rol: admin', '2024-10-15 14:21:49'),
(2, 'DELETE', 'jugada', 116, 'Jugada eliminada: Lanzamiento: 7, Apuesta: 1, Saldo Inicial: 101, Saldo Final: 101, Hora: 2024-10-10 16:55:08', '2024-10-15 14:21:49'),
(3, 'DELETE', 'jugada', 117, 'Jugada eliminada: Lanzamiento: 4, Apuesta: 1, Saldo Inicial: 99, Saldo Final: 100, Hora: 2024-10-10 16:55:09', '2024-10-15 14:21:49'),
(4, 'DELETE', 'jugada', 118, 'Jugada eliminada: Lanzamiento: 7, Apuesta: 1, Saldo Inicial: 101, Saldo Final: 101, Hora: 2024-10-10 16:55:10', '2024-10-15 14:21:49'),
(5, 'DELETE', 'jugada', 119, 'Jugada eliminada: Lanzamiento: 7, Apuesta: 1, Saldo Inicial: 102, Saldo Final: 102, Hora: 2024-10-10 17:35:56', '2024-10-15 14:21:49'),
(6, 'DELETE', 'jugada', 120, 'Jugada eliminada: Lanzamiento: 5, Apuesta: 1, Saldo Inicial: 100, Saldo Final: 101, Hora: 2024-10-10 17:35:57', '2024-10-15 14:21:49'),
(7, 'DELETE', 'jugada', 121, 'Jugada eliminada: Lanzamiento: 6, Apuesta: 1, Saldo Inicial: 99, Saldo Final: 100, Hora: 2024-10-10 17:35:58', '2024-10-15 14:21:49'),
(9, 'DELETE', 'jugador', 42, 'Jugador eliminado: Nombre: dani, Apellidos: fernandez, DNI: 123123, Edad: 46, Sexo: masculino, Apodo: fumapetas, Saldo: 96, Email: pruebasalexander05@gmail.com, Rol: usuario', '2024-10-15 16:11:33'),
(10, 'DELETE', 'jugada', 122, 'Jugada eliminada: Lanzamiento: 4, Apuesta: 1, Saldo Inicial: 98, Saldo Final: 99, Hora: 2024-10-15 18:06:42', '2024-10-15 16:11:33'),
(11, 'DELETE', 'jugada', 123, 'Jugada eliminada: Lanzamiento: 4, Apuesta: 1, Saldo Inicial: 97, Saldo Final: 98, Hora: 2024-10-15 18:06:43', '2024-10-15 16:11:33'),
(12, 'DELETE', 'jugada', 124, 'Jugada eliminada: Lanzamiento: 9, Apuesta: 2, Saldo Inicial: 94, Saldo Final: 96, Hora: 2024-10-15 18:06:45', '2024-10-15 16:11:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugada`
--

CREATE TABLE `jugada` (
  `id_jugada` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `lanzamiento` int(2) NOT NULL,
  `hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `apuesta` float NOT NULL,
  `saldo_inicial` float NOT NULL,
  `saldo_final` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugada`
--

INSERT INTO `jugada` (`id_jugada`, `id_jugador`, `lanzamiento`, `hora`, `apuesta`, `saldo_inicial`, `saldo_final`) VALUES
(125, 43, 7, '2024-10-15 16:13:21', 1, 101, 101),
(126, 43, 3, '2024-10-15 16:13:23', 1, 99, 100),
(127, 43, 8, '2024-10-15 16:13:24', 1, 98, 99),
(128, 43, 6, '2024-10-15 16:13:25', 1, 97, 98),
(129, 43, 4, '2024-10-15 16:13:40', 1, 96, 97),
(130, 43, 9, '2024-10-15 16:13:42', 1, 95, 96);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador`
--

CREATE TABLE `jugador` (
  `id_jugador` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellidos` varchar(45) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `edad` int(11) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `saldo` float NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha` date DEFAULT NULL,
  `apodo` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugador`
--

INSERT INTO `jugador` (`id_jugador`, `nombre`, `apellidos`, `dni`, `edad`, `sexo`, `saldo`, `contrasena`, `fecha`, `apodo`, `email`, `rol`) VALUES
(41, 'Alexander', 'Valdivia', 'Y9228879M', 22, 'masculino', 100, '$2y$10$bJdqpRPEsH/GWpnnAmzZVuEMLyoUOD4bBVBqQZJ46HdiI1gh/BsXa', '2024-10-15', 'Alexinzler', 'pruebasalexander05@gmail.com', 'admin'),
(43, 'dani', 'boludo', '123123', 47, 'masculino', 96, '$2y$10$M/N5rOvZDOqOhxKqngAhHuG3qqhYVPJGfr5YOKrgVcmlxXMiy1nK6', '2024-10-15', 'fumapetas', 'danielfer222222@gmail.com', 'usuario');

--
-- Disparadores `jugador`
--
DELIMITER $$
CREATE TRIGGER `auditar_delete_jugador` BEFORE DELETE ON `jugador` FOR EACH ROW BEGIN
    -- Insertar los datos del jugador eliminado en la auditoría
    INSERT INTO `auditoria` (`accion`, `tabla`, `id_registro`, `detalle`, `fecha`)
    VALUES ('DELETE', 'jugador', OLD.id_jugador, 
            CONCAT('Jugador eliminado: Nombre: ', OLD.nombre, 
                   ', Apellidos: ', OLD.apellidos, 
                   ', DNI: ', OLD.dni, 
                   ', Edad: ', OLD.edad, 
                   ', Sexo: ', OLD.sexo, 
                   ', Apodo: ', OLD.apodo, 
                   ', Saldo: ', OLD.saldo, 
                   ', Email: ', OLD.email, 
                   ', Rol: ', OLD.rol), 
            NOW());

    -- Insertar los datos de las jugadas del jugador eliminado en la auditoría
    INSERT INTO `auditoria` (`accion`, `tabla`, `id_registro`, `detalle`, `fecha`)
    SELECT 'DELETE', 'jugada', j.id_jugada, 
           CONCAT('Jugada eliminada: Lanzamiento: ', j.lanzamiento, 
                  ', Apuesta: ', j.apuesta, 
                  ', Saldo Inicial: ', j.saldo_inicial, 
                  ', Saldo Final: ', j.saldo_final, 
                  ', Hora: ', j.hora),
           NOW()
    FROM jugada j
    WHERE j.id_jugador = OLD.id_jugador;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_auditoria`);

--
-- Indices de la tabla `jugada`
--
ALTER TABLE `jugada`
  ADD PRIMARY KEY (`id_jugada`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD PRIMARY KEY (`id_jugador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `jugada`
--
ALTER TABLE `jugada`
  MODIFY `id_jugada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT de la tabla `jugador`
--
ALTER TABLE `jugador`
  MODIFY `id_jugador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `jugada`
--
ALTER TABLE `jugada`
  ADD CONSTRAINT `jugada_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id_jugador`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
