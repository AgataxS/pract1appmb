-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2024 a las 19:33:09
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
-- Base de datos: `practica1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id_asignacion` int(11) NOT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `id_horario` int(11) DEFAULT NULL,
  `estado_asignacion` enum('activa','inactiva') DEFAULT 'activa',
  `cupo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id_asignacion`, `id_materia`, `id_docente`, `id_aula`, `id_horario`, `estado_asignacion`, `cupo`) VALUES
(1, 1, 1, 1, 1, 'inactiva', 5),
(2, 1, 1, 1, 1, 'inactiva', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `id_aula` int(11) NOT NULL,
  `nombre_aula` varchar(50) NOT NULL,
  `numero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`id_aula`, `nombre_aula`, `numero`) VALUES
(1, 'A', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargas_horarias`
--

CREATE TABLE `cargas_horarias` (
  `id_carga_horaria` int(11) NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_asignacion` int(11) DEFAULT NULL,
  `horas_asignadas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `facultad_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`, `facultad_id`) VALUES
(1, 'ING SISTEMAS', 1),
(2, 'ING SISTEMAS', 1),
(3, 'Runay', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `turno` enum('mañana','tarde','noche') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id_docente`, `nombre`, `apellido`, `telefono`, `turno`) VALUES
(1, 'DOCENTE NEW', 'DOCENTE ', '98585', 'mañana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `carrera_id` int(11) DEFAULT NULL,
  `ci` varchar(20) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id_estudiante`, `nombre`, `apellido`, `correo`, `carrera_id`, `ci`, `imagen`, `usuario_id`) VALUES
(1, 'Javier Agustin', '122121 Ali Tejerina', 'drogsmenx@gmail.com', 1, '6689290', '/uploads/67030763a5241.png', NULL),
(2, 'Javier Agustin', '122121 Ali Tejerina', 'drogsmenx@gmail.com', 1, '6689290', NULL, 3),
(3, 'jose', 'jos', 'upd@gmail.com', 2, '34556453435', 'uploads/6703128f22c8f.png', 4),
(4, 'cas', 'pedro', 'pedro@gmail.com', 1, '123213', 'uploads/67031c616653a.png', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultades`
--

CREATE TABLE `facultades` (
  `id_facultad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facultades`
--

INSERT INTO `facultades` (`id_facultad`, `nombre`) VALUES
(1, 'TECNOLOGIA'),
(2, 'ECONOMIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultades_docentes`
--

CREATE TABLE `facultades_docentes` (
  `id_facultad` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultades_materias`
--

CREATE TABLE `facultades_materias` (
  `id_facultad` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `id_asignacion` int(11) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `turno` enum('mañana','tarde','noche') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `receso` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `turno`, `hora_inicio`, `hora_fin`, `receso`) VALUES
(1, 'mañana', '08:00:00', '11:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `periodo_id` int(11) DEFAULT NULL,
  `grupo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id_inscripcion`, `id_estudiante`, `id_materia`, `fecha_inscripcion`, `estado`, `periodo_id`, `grupo_id`) VALUES
(3, 3, 1, '2024-10-06 22:44:41', 'pendiente', NULL, NULL),
(8, 3, 2, '2024-10-06 23:02:13', 'pendiente', NULL, NULL),
(9, 4, 1, '2024-10-06 23:25:34', 'pendiente', NULL, NULL);

--
-- Disparadores `inscripciones`
--
DELIMITER $$
CREATE TRIGGER `incrementar_popularidad` AFTER INSERT ON `inscripciones` FOR EACH ROW BEGIN
    INSERT INTO popularidad_materias (id_materia, numero_solicitantes)
    VALUES (NEW.id_materia, 1)
    ON DUPLICATE KEY UPDATE numero_solicitantes = numero_solicitantes + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `estado` enum('abierta','cerrada') DEFAULT 'cerrada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre`, `codigo`, `estado`) VALUES
(1, 'SISTEMAS DIGITALES', 'SISDIG1', 'abierta'),
(2, 'MATERIA NUEVA', 'MAT0', 'abierta'),
(3, 'MATERIA NEW', 'MANE1', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos_academicos`
--

CREATE TABLE `periodos_academicos` (
  `id_periodo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `popularidad_materias`
--

CREATE TABLE `popularidad_materias` (
  `id_materia` int(11) NOT NULL,
  `numero_solicitantes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `popularidad_materias`
--

INSERT INTO `popularidad_materias` (`id_materia`, `numero_solicitantes`) VALUES
(1, 2),
(2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prerrequisitos`
--

CREATE TABLE `prerrequisitos` (
  `id_materia` int(11) NOT NULL,
  `id_prerrequisito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL,
  `estudiante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `role`, `estudiante_id`) VALUES
(1, 'admin', '$2y$10$lNCLGBl1NHcre4ctrLpBRet.z9Ays9DTuH1/OKF32EgGaoFq8Lft.', 'admin', NULL),
(2, 'javier agustin.122121 ali tejerina', '$2y$10$JppjqDgKvDKpyG5Pi7sy5.pEuXlq6T3sgz4YplULYBZUYkJH.uw8O', '', 1),
(3, 'javier', '$2y$10$Ybq2UK1mjpdN5biiK9i9rONFXsM3utwvUfSxASYAeZ4f/WqHQte1O', '', NULL),
(4, 'jose', '$2y$10$FmsOAj7SM2BimfYsMNHn5O8n/0v1n5XmNsg9rG1SEH3r5yCIckOG6', 'student', NULL),
(5, 'cas', '$2y$10$CLPi8ZAGm346jRVE1D6kQ.LZbgHACl.uoQqNe.BJ3cVgOPmJlcJ2e', 'student', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_docente` (`id_docente`),
  ADD KEY `id_aula` (`id_aula`),
  ADD KEY `id_horario` (`id_horario`);

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id_aula`);

--
-- Indices de la tabla `cargas_horarias`
--
ALTER TABLE `cargas_horarias`
  ADD PRIMARY KEY (`id_carga_horaria`),
  ADD KEY `id_docente` (`id_docente`),
  ADD KEY `id_asignacion` (`id_asignacion`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`),
  ADD KEY `facultad_id` (`facultad_id`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id_docente`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD KEY `carrera_id` (`carrera_id`);

--
-- Indices de la tabla `facultades`
--
ALTER TABLE `facultades`
  ADD PRIMARY KEY (`id_facultad`);

--
-- Indices de la tabla `facultades_docentes`
--
ALTER TABLE `facultades_docentes`
  ADD PRIMARY KEY (`id_facultad`,`id_docente`),
  ADD KEY `id_docente` (`id_docente`);

--
-- Indices de la tabla `facultades_materias`
--
ALTER TABLE `facultades_materias`
  ADD PRIMARY KEY (`id_facultad`,`id_materia`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `id_asignacion` (`id_asignacion`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `periodo_id` (`periodo_id`),
  ADD KEY `grupo_id` (`grupo_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `periodos_academicos`
--
ALTER TABLE `periodos_academicos`
  ADD PRIMARY KEY (`id_periodo`);

--
-- Indices de la tabla `popularidad_materias`
--
ALTER TABLE `popularidad_materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `prerrequisitos`
--
ALTER TABLE `prerrequisitos`
  ADD PRIMARY KEY (`id_materia`,`id_prerrequisito`),
  ADD KEY `id_prerrequisito` (`id_prerrequisito`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `estudiante_id` (`estudiante_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id_aula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cargas_horarias`
--
ALTER TABLE `cargas_horarias`
  MODIFY `id_carga_horaria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `facultades`
--
ALTER TABLE `facultades`
  MODIFY `id_facultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `periodos_academicos`
--
ALTER TABLE `periodos_academicos`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `asignaciones_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `asignaciones_ibfk_2` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`),
  ADD CONSTRAINT `asignaciones_ibfk_3` FOREIGN KEY (`id_aula`) REFERENCES `aulas` (`id_aula`),
  ADD CONSTRAINT `asignaciones_ibfk_4` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`);

--
-- Filtros para la tabla `cargas_horarias`
--
ALTER TABLE `cargas_horarias`
  ADD CONSTRAINT `cargas_horarias_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`),
  ADD CONSTRAINT `cargas_horarias_ibfk_2` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`);

--
-- Filtros para la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD CONSTRAINT `carreras_ibfk_1` FOREIGN KEY (`facultad_id`) REFERENCES `facultades` (`id_facultad`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `estudiantes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `facultades_docentes`
--
ALTER TABLE `facultades_docentes`
  ADD CONSTRAINT `facultades_docentes_ibfk_1` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`),
  ADD CONSTRAINT `facultades_docentes_ibfk_2` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`);

--
-- Filtros para la tabla `facultades_materias`
--
ALTER TABLE `facultades_materias`
  ADD CONSTRAINT `facultades_materias_ibfk_1` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`),
  ADD CONSTRAINT `facultades_materias_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_academicos` (`id_periodo`),
  ADD CONSTRAINT `inscripciones_ibfk_4` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`);

--
-- Filtros para la tabla `popularidad_materias`
--
ALTER TABLE `popularidad_materias`
  ADD CONSTRAINT `popularidad_materias_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `prerrequisitos`
--
ALTER TABLE `prerrequisitos`
  ADD CONSTRAINT `prerrequisitos_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `prerrequisitos_ibfk_2` FOREIGN KEY (`id_prerrequisito`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
