-- phpMyAdmin SQL Dump
-- version 3.5.2.1
-- http://www.phpmyadmin.net
--
-- Servidor: 192.168.3.47
-- Tiempo de generación: 17-02-2014 a las 19:00:06
-- Versión del servidor: 5.1.54-log
-- Versión de PHP: 5.3.3-7+squeeze15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `bd-rap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perla` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `texto` varchar(251) NOT NULL,
  `fecha_subida` datetime NOT NULL,
  `fecha_modificacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `perla` (`perla`,`usuario`),
  KEY `usuario` (`usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Disparadores `comentarios`
--
DROP TRIGGER IF EXISTS `comentario_borrado`;
DELIMITER //
CREATE TRIGGER `comentario_borrado` AFTER DELETE ON `comentarios`
 FOR EACH ROW BEGIN
    UPDATE logros SET num_comentarios = num_comentarios - 1 WHERE logros.usuario = OLD.usuario AND logros.mes = MONTH( OLD.fecha_subida ) AND logros.anno = YEAR( OLD.fecha_subida );
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `nuevo_comentario`;
DELIMITER //
CREATE TRIGGER `nuevo_comentario` AFTER INSERT ON `comentarios`
 FOR EACH ROW BEGIN
    INSERT INTO logros (usuario, mes, anno, num_comentarios) VALUES( NEW.usuario, MONTH( NEW.fecha_subida ), YEAR( NEW.fecha_subida ), 1 ) ON DUPLICATE KEY UPDATE num_comentarios = num_comentarios + 1;
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE IF NOT EXISTS `etiquetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1131 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE IF NOT EXISTS `logros` (
  `usuario` int(11) NOT NULL,
  `mes` tinyint(3) unsigned NOT NULL,
  `anno` smallint(5) unsigned NOT NULL,
  `num_perlas` smallint(5) unsigned NOT NULL,
  `num_perlas_calificadas` smallint(5) unsigned NOT NULL,
  `num_comentarios` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`usuario`,`mes`,`anno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_email`
--

CREATE TABLE IF NOT EXISTS `notificaciones_email` (
  `usuario` int(11) NOT NULL,
  `nueva_perla` enum('siempre','participante','nunca') NOT NULL DEFAULT 'participante',
  `nuevo_comentario` enum('siempre','participante','nunca') NOT NULL DEFAULT 'participante',
  `nueva_nota` enum('siempre','participante','nunca') NOT NULL DEFAULT 'nunca',
  `nuevo_usuario` enum('siempre','nunca') NOT NULL DEFAULT 'siempre',
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE IF NOT EXISTS `participantes` (
  `perla` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  PRIMARY KEY (`perla`,`usuario`),
  KEY `perla` (`perla`,`usuario`),
  KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perlas`
--

CREATE TABLE IF NOT EXISTS `perlas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(75) CHARACTER SET utf8 NOT NULL,
  `texto` text CHARACTER SET utf8 NOT NULL,
  `fecha_subida` datetime NOT NULL,
  `fecha` varchar(50) CHARACTER SET utf8 NOT NULL,
  `subidor` int(11) NOT NULL,
  `fecha_modificacion` datetime NOT NULL,
  `modificador` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `titulo` (`titulo`),
  KEY `fecha_subida` (`fecha_subida`),
  KEY `subidor` (`subidor`),
  KEY `modificador` (`modificador`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=208 ;

--
-- Disparadores `perlas`
--
DROP TRIGGER IF EXISTS `nueva_perla`;
DELIMITER //
CREATE TRIGGER `nueva_perla` AFTER INSERT ON `perlas`
 FOR EACH ROW BEGIN
    INSERT INTO logros (usuario, mes, anno, num_perlas) VALUES( NEW.subidor, MONTH( NEW.fecha_subida ), YEAR( NEW.fecha_subida ), 1 ) ON DUPLICATE KEY UPDATE num_perlas = num_perlas + 1;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `perla_borrada`;
DELIMITER //
CREATE TRIGGER `perla_borrada` AFTER DELETE ON `perlas`
 FOR EACH ROW BEGIN
    UPDATE logros SET num_perlas = num_perlas - 1 WHERE logros.usuario = OLD.subidor AND logros.mes = MONTH( OLD.fecha_subida ) AND logros.anno = YEAR( OLD.fecha_subida );
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perlas_etiquetas`
--

CREATE TABLE IF NOT EXISTS `perlas_etiquetas` (
  `perla` int(11) NOT NULL,
  `etiqueta` int(11) NOT NULL,
  PRIMARY KEY (`perla`,`etiqueta`),
  KEY `perla` (`perla`),
  KEY `etiqueta` (`etiqueta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) CHARACTER SET utf8 NOT NULL,
  `contrasenna` varchar(32) CHARACTER SET ascii NOT NULL,
  `email` varchar(32) DEFAULT NULL,
  `cod_validacion_email` varchar(32) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_conexion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `email` (`email`,`cod_validacion_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `votos`
--

CREATE TABLE IF NOT EXISTS `votos` (
  `perla` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`perla`,`usuario`),
  KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Disparadores `votos`
--
DROP TRIGGER IF EXISTS `borrar_voto`;
DELIMITER //
CREATE TRIGGER `borrar_voto` AFTER DELETE ON `votos`
 FOR EACH ROW BEGIN
    UPDATE logros SET num_perlas_calificadas = num_perlas_calificadas - 1  WHERE logros.usuario = OLD.usuario AND logros.mes = MONTH( OLD.fecha ) AND logros.anno = YEAR( OLD.fecha );
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `nuevo_voto`;
DELIMITER //
CREATE TRIGGER `nuevo_voto` AFTER INSERT ON `votos`
 FOR EACH ROW BEGIN
    INSERT INTO logros (usuario, mes, anno, num_perlas_calificadas) VALUES( NEW.usuario, MONTH( NEW.fecha ), YEAR( NEW.fecha ), 1 ) ON DUPLICATE KEY UPDATE num_perlas_calificadas = num_perlas_calificadas + 1;
  END
//
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_3` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logros`
--
ALTER TABLE `logros`
  ADD CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `notificaciones_email`
--
ALTER TABLE `notificaciones_email`
  ADD CONSTRAINT `notificaciones_email_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `participantes_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `participantes_ibfk_3` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `perlas`
--
ALTER TABLE `perlas`
  ADD CONSTRAINT `perlas_ibfk_2` FOREIGN KEY (`subidor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `perlas_ibfk_3` FOREIGN KEY (`modificador`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `votos_ibfk_3` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
