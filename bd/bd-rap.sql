-- phpMyAdmin SQL Dump
-- version 3.5.2.1
-- http://www.phpmyadmin.net
--
-- Servidor: 192.168.3.47
-- Tiempo de generación: 22-01-2013 a las 20:15:10
-- Versión del servidor: 5.1.54-log
-- Versión de PHP: 5.3.3-7+squeeze13

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
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8 NOT NULL,
  `num_perlas` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=20 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Disparadores `comentarios`
--
DROP TRIGGER IF EXISTS `comentario_borrado`;
DELIMITER //
CREATE TRIGGER `comentario_borrado` AFTER DELETE ON `comentarios`
 FOR EACH ROW BEGIN
    UPDATE perlas SET num_comentarios = num_comentarios - 1 WHERE perlas.id = OLD.perla;
    UPDATE logros SET num_comentarios = num_comentarios - 1 WHERE logros.usuario = OLD.usuario AND logros.mes = MONTH( OLD.fecha_subida ) AND logros.anno = YEAR( OLD.fecha_subida );
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `nuevo_comentario`;
DELIMITER //
CREATE TRIGGER `nuevo_comentario` AFTER INSERT ON `comentarios`
 FOR EACH ROW BEGIN
    UPDATE perlas SET num_comentarios = num_comentarios + 1 WHERE perlas.id = NEW.perla;
    INSERT INTO logros (usuario, mes, anno, num_comentarios) VALUES( NEW.usuario, MONTH( NEW.fecha_subida ), YEAR( NEW.fecha_subida ), 1 ) ON DUPLICATE KEY UPDATE num_comentarios = num_comentarios + 1;
  END
//
DELIMITER ;

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
  `contenido_informatico` tinyint(1) NOT NULL,
  `humor_negro` tinyint(1) NOT NULL,
  `perla_visual` tinyint(1) NOT NULL,
  `categoria` int(11) NOT NULL,
  `subidor` int(11) NOT NULL,
  `fecha_modificacion` datetime NOT NULL,
  `modificador` int(11) DEFAULT NULL,
  `nota_acumulada` int(11) NOT NULL DEFAULT '0',
  `num_votos` int(11) NOT NULL DEFAULT '0',
  `num_comentarios` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoria` (`categoria`,`titulo`),
  KEY `fecha_subida` (`fecha_subida`,`categoria`),
  KEY `categoria_2` (`categoria`),
  KEY `subidor` (`subidor`),
  KEY `modificador` (`modificador`),
  KEY `nota_acumulada` (`nota_acumulada`,`num_comentarios`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=150 ;

--
-- Disparadores `perlas`
--
DROP TRIGGER IF EXISTS `nueva_perla`;
DELIMITER //
CREATE TRIGGER `nueva_perla` AFTER INSERT ON `perlas`
 FOR EACH ROW BEGIN
    INSERT INTO logros (usuario, mes, anno, num_perlas) VALUES( NEW.subidor, MONTH( NEW.fecha_subida ), YEAR( NEW.fecha_subida ), 1 ) ON DUPLICATE KEY UPDATE num_perlas = num_perlas + 1;
	 UPDATE categorias SET num_perlas = num_perlas + 1 WHERE categorias.id = NEW.categoria;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `perla_actualizada`;
DELIMITER //
CREATE TRIGGER `perla_actualizada` AFTER UPDATE ON `perlas`
 FOR EACH ROW BEGIN
	 UPDATE categorias SET num_perlas = num_perlas - 1 WHERE categorias.id = OLD.categoria;
	 UPDATE categorias SET num_perlas = num_perlas + 1 WHERE categorias.id = NEW.categoria;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `perla_borrada`;
DELIMITER //
CREATE TRIGGER `perla_borrada` BEFORE DELETE ON `perlas`
 FOR EACH ROW BEGIN
    UPDATE logros SET num_perlas = num_perlas - 1 WHERE logros.usuario = OLD.subidor AND logros.mes = MONTH( OLD.fecha_subida ) AND logros.anno = YEAR( OLD.fecha_subida );
	 UPDATE categorias SET num_perlas = num_perlas - 1 WHERE categorias.id = OLD.categoria;
	 DELETE FROM comentarios WHERE perla = OLD.id;
	 DELETE FROM participantes WHERE perla = OLD.id;
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) CHARACTER SET utf8 NOT NULL,
  `contrasenna` varchar(32) CHARACTER SET ascii NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

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
    UPDATE perlas SET nota_acumulada = nota_acumulada - OLD.nota, num_votos = num_votos - 1 WHERE id = OLD.perla;
    UPDATE logros SET num_perlas_calificadas = num_perlas_calificadas - 1  WHERE logros.usuario = OLD.usuario AND logros.mes = MONTH( OLD.fecha ) AND logros.anno = YEAR( OLD.fecha );
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `cambio_voto`;
DELIMITER //
CREATE TRIGGER `cambio_voto` AFTER UPDATE ON `votos`
 FOR EACH ROW BEGIN
    UPDATE perlas SET nota_acumulada = nota_acumulada + NEW.nota - OLD.nota WHERE id = NEW.perla;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `nuevo_voto`;
DELIMITER //
CREATE TRIGGER `nuevo_voto` AFTER INSERT ON `votos`
 FOR EACH ROW BEGIN
    UPDATE perlas SET nota_acumulada = nota_acumulada + NEW.nota, num_votos = num_votos + 1 WHERE id = NEW.perla;
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
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `logros`
--
ALTER TABLE `logros`
  ADD CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `participantes_ibfk_1` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`),
  ADD CONSTRAINT `participantes_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `perlas`
--
ALTER TABLE `perlas`
  ADD CONSTRAINT `perlas_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `perlas_ibfk_2` FOREIGN KEY (`subidor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `perlas_ibfk_3` FOREIGN KEY (`modificador`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `votos_ibfk_1` FOREIGN KEY (`perla`) REFERENCES `perlas` (`id`),
  ADD CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
