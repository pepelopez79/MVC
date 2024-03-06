-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para bdblog
CREATE DATABASE IF NOT EXISTS `bdblog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bdblog`;

-- Volcando estructura para tabla bdblog.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `IDCAT` int(5) NOT NULL AUTO_INCREMENT,
  `NOMBRECAT` varchar(40) NOT NULL,
  PRIMARY KEY (`IDCAT`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla bdblog.categorias: ~11 rows (aproximadamente)
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` (`IDCAT`, `NOMBRECAT`) VALUES
	(1, 'Terror'),
	(2, 'Fantasía'),
	(24, 'Ciencia Ficción'),
	(25, 'Comedia'),
	(30, 'Drama'),
	(31, 'Romance'),
	(32, 'Musical'),
	(33, 'Animación'),
	(34, 'Acción'),
	(35, 'Documental');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;

-- Volcando estructura para tabla bdblog.entradas
CREATE TABLE IF NOT EXISTS `entradas` (
  `IDENT` int(5) NOT NULL AUTO_INCREMENT,
  `IDUSUARIO` int(5) NOT NULL,
  `IDCATEGORIA` int(5) NOT NULL,
  `TITULO` varchar(40) NOT NULL,
  `IMAGEN` varchar(40) NOT NULL,
  `DESCRIPCION` text NOT NULL,
  `FECHA` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`IDENT`),
  KEY `IDUSUARIO` (`IDUSUARIO`),
  KEY `IDCATEGORIA` (`IDCATEGORIA`),
  CONSTRAINT `ENTRADAS_IBFK_1` FOREIGN KEY (`IDUSUARIO`) REFERENCES `usuarios` (`IDUSER`) ON UPDATE CASCADE,
  CONSTRAINT `ENTRADAS_IBFK_2` FOREIGN KEY (`IDCATEGORIA`) REFERENCES `categorias` (`IDCAT`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla bdblog.entradas: ~10 rows (aproximadamente)
/*!40000 ALTER TABLE `entradas` DISABLE KEYS */;
INSERT INTO `entradas` (`IDENT`, `IDUSUARIO`, `IDCATEGORIA`, `TITULO`, `IMAGEN`, `DESCRIPCION`, `FECHA`) VALUES
	(23, 1, 1, 'Five Nights At Freddy', '1709682129-fivenightsaf.jpg', '<p><i><strong>Five Nights at Freddy\'s</strong></i>, (en su traducción al español como "Cinco noches en Freddy\'s") abreviado como <i>FNaF</i>, es una <a href="https://es.wikipedia.org/wiki/Franquicia_de_medios">franquicia de medios</a> basada en una serie de <a href="https://es.wikipedia.org/wiki/Videojuegos_de_terror">videojuegos de terror</a> <a href="https://es.wikipedia.org/wiki/Videojuego_independiente">independientes</a> creada, diseñada, desarrollada y publicada por <a href="https://es.wikipedia.org/wiki/Scott_Cawthon">Scott Cawthon</a>. La serie se centra en la historia de una <a href="https://es.wikipedia.org/wiki/Pizzer%C3%ADa">pizzería</a> llamada <i>Freddy Fazbear\'s Pizza</i>.</p>', '2024-03-06 00:42:09'),
	(24, 1, 1, 'Black Friday', '1709681762-blackfriday.jpg', '<p><i><strong>Black Friday</strong></i> es una <a href="https://es.wikipedia.org/wiki/Pel%C3%ADcula">película</a> de <a href="https://es.wikipedia.org/wiki/Comedia_de_terror">comedia de terror</a> <a href="https://es.wikipedia.org/wiki/Estados_Unidos">estadounidense</a> de 2021 dirigida por <a href="https://es.wikipedia.org/w/index.php?title=Casey_Tebo&amp;action=edit&amp;redlink=1">Casey Tebo</a> y protagonizada por <a href="https://es.wikipedia.org/wiki/Devon_Sawa">Devon Sawa</a>, <a href="https://es.wikipedia.org/wiki/Ivana_Baquero">Ivana Baquero</a>, <a href="https://es.wikipedia.org/wiki/Ryan_Lee">Ryan Lee</a>, Stephen Peck, <a href="https://es.wikipedia.org/wiki/Michael_Jai_White">Michael Jai White</a> y <a href="https://es.wikipedia.org/wiki/Bruce_Campbell">Bruce Campbell</a>.</p>', '2024-03-06 00:42:44'),
	(33, 5, 34, 'Hipnosis', '1709742003-hipnosis.jpg', '<p>Decidido a encontrar a su hija desaparecida, el detective<strong> Danny Rourke (Ben Affleck) </strong>se ve inmerso en un laberinto mientras investiga una serie de asaltos a bancos que desafían la realidad y que le harán cuestionarse sobre todo y todos los que le rodean.</p>', '2024-03-06 17:20:03'),
	(35, 2, 33, 'Wish: El Poder de los Deseos', '1709743422-wishpoderdeseos.jpg', '<p><strong>"Wish: El poder de los deseos"</strong>, de <i>Walt Disney Animation Studios</i>, es una comedia musical de animación que nos da la bienvenida al reino mágico de <i>Rosas</i>, donde <i>Asha</i>, una idealista e ingeniosa joven, pide un deseo tan poderoso que es respondido por una fuerza cósmica; una pequeña bola de energía ilimitada llamada <i>Star</i><strong>.</strong></p>', '2024-03-06 18:41:13'),
	(40, 2, 35, 'Napoleón', '1709681770-napoleon.jpg', '<p><i><strong>Napoleón</strong></i> (en inglés: <i><strong>Napoleon</strong></i>) es una <a href="https://es.wikipedia.org/wiki/Drama_hist%C3%B3rico_(cinematograf%C3%ADa)">película de drama histórico</a> <a href="https://es.wikipedia.org/wiki/Cine_%C3%A9pico">épico</a> dirigida y producida por <a href="https://es.wikipedia.org/wiki/Ridley_Scott">Ridley Scott</a> y escrita por <a href="https://es.wikipedia.org/w/index.php?title=David_Scarpa&amp;action=edit&amp;redlink=1">David Scarpa</a>. Basada en la historia de <a href="https://es.wikipedia.org/wiki/Napole%C3%B3n_Bonaparte">Napoleón Bonaparte</a>, la película relata principalmente su ascenso al poder y su relación con la <a href="https://es.wikipedia.org/wiki/Josefina_de_Beauharnais">emperatriz Josefina</a>.</p>', '2024-03-06 17:13:21'),
	(52, 5, 25, 'Ocho Apellidos Marroquís', '1709682205-ochoapellidosmarroquis.jpg', '<p><i><strong>Ocho apellidos marroquís</strong></i> es una <a href="https://es.wikipedia.org/wiki/Pel%C3%ADcula">película</a> <a href="https://es.wikipedia.org/wiki/Espa%C3%B1a">española</a> del año 2023, dirigida por <a href="https://es.wikipedia.org/wiki/%C3%81lvaro_Fern%C3%A1ndez_Armero">Álvaro Fernández Armero</a> y protagonizada por <a href="https://es.wikipedia.org/wiki/Michelle_Jenner">Michelle Jenner</a> y <a href="https://es.wikipedia.org/wiki/Juli%C3%A1n_L%C3%B3pez_(actor)">Julián López</a>. Es la <a href="https://es.wikipedia.org/wiki/Secuela">secuela</a> espiritual de <a href="https://es.wikipedia.org/wiki/Ocho_apellidos_vascos"><i>Ocho apellidos vascos</i></a> y <a href="https://es.wikipedia.org/wiki/Ocho_apellidos_catalanes"><i>Ocho apellidos catalanes</i></a>.</p>', '2024-03-06 00:43:32'),
	(53, 2, 25, 'Alimanias', '1709741751-alimanias.jpg', '<p>Dos hermanos muy distintos entre ellos, <strong>Carlos</strong> y <strong>Paco (Carlos Areces y Jordi Sánchez)</strong>, acaban unidos por la ambición de heredar un edificio propiedad de su madre, ya anciana. En ese lugar ambos tienen puestas todas sus esperanzas para mejorar su precaria situación económica.</p>', '2024-03-06 17:15:51'),
	(54, 5, 32, 'Callas', '1709741683-callas.jpg', '<p>Película sobre la <i>cantate de ópera griega</i><strong> Maria Callas (Noomi Rapace)</strong> cuya voz y tormentosa relación con el magnate griego <strong>Aristóteles Onassis</strong> forman parte de la historia del<i> Siglo XX</i>.</p>', '2024-03-06 18:59:47'),
	(55, 5, 2, 'Los Juegos del Hambre', '1709748210-losjuegosdelhambre.jpg', '<p><i><strong>Los juegos del hambre</strong></i> (<i><strong>The Hunger Games</strong></i>) es una película de <a href="https://es.wikipedia.org/wiki/Ciencia_ficci%C3%B3n">ciencia ficción</a>, <a href="https://es.wikipedia.org/wiki/Cine_de_acci%C3%B3n">acción</a> y <a href="https://es.wikipedia.org/wiki/Drama">drama</a>. Dirigida por <a href="https://es.wikipedia.org/wiki/Gary_Ross">Gary Ross</a> y basada en <a href="https://es.wikipedia.org/wiki/Los_juegos_del_hambre">la novela best-seller del mismo nombre</a> de <a href="https://es.wikipedia.org/wiki/Suzanne_Collins">Suzanne Collins</a>.</p>', '2024-03-06 19:03:39');
/*!40000 ALTER TABLE `entradas` ENABLE KEYS */;

-- Volcando estructura para tabla bdblog.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `IDUSER` int(5) NOT NULL AUTO_INCREMENT,
  `NICK` varchar(40) NOT NULL,
  `NOMBRE` varchar(40) NOT NULL,
  `APELLIDOS` varchar(40) NOT NULL,
  `EMAIL` varchar(40) NOT NULL,
  `CONTRASENIA` varchar(40) NOT NULL,
  `AVATAR` varchar(50) NOT NULL,
  `ROL` varchar(40) NOT NULL,
  PRIMARY KEY (`IDUSER`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla bdblog.usuarios: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`IDUSER`, `NICK`, `NOMBRE`, `APELLIDOS`, `EMAIL`, `CONTRASENIA`, `AVATAR`, `ROL`) VALUES
	(1, 'malodo', 'Maria', 'Lopez Dominguez', 'maria@gmail.com', 'maria1234', 'Perfil.jpg', 'admin'),
	(2, 'ninja', 'Antonio', 'Gonzalez', 'antonio@gmail.com', '12345', 'Perfil.jpg', 'user'),
	(5, 'pepe', 'Pepe', 'López', 'pepe@gmail.com', 'pepe1234', 'Wallpaper.jpg', 'admin'),
	(15, 'franmark', 'Fran', 'Marquez', 'fmarz@gmail.com', 'sdf', 'Fondo2.png', 'user'),
	(16, 'juanito', 'Juan', 'Pérez', 'juaniiito@gmail.com', 'juan1234', 'Fondo4.png', 'user'),
	(17, 'admin', 'Administrador', 'Admin', 'admin@gmail.com', 'admin1234', 'Wallpaper.jpg', 'admin'),
	(18, 'jc12', 'jotac', '12', 'jc12@gmail.com', 'jc121234', 'Fondo4.png', 'user');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
