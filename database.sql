-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.9-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura do banco de dados para mastermind_db
CREATE DATABASE IF NOT EXISTS `mastermind_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `mastermind_db`;


-- Copiando estrutura para tabela mastermind_db.tb_color
CREATE TABLE IF NOT EXISTS `tb_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` char(1) NOT NULL,
  `html_code` char(7) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_u_color_short_name` (`short_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_color: ~8 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_color` DISABLE KEYS */;
INSERT INTO `tb_color` (`id`, `name`, `short_name`, `html_code`, `dt_created`) VALUES
	(1, 'Red', 'R', '#FF0000', '2016-05-20 23:04:14'),
	(2, 'Green', 'G', '#00FF00', '2016-05-20 23:04:47'),
	(3, 'Blue', 'B', '#0000FF', '2016-05-20 23:04:59'),
	(4, 'Yellow', 'Y', '#FFFF00', '2016-05-20 23:05:44'),
	(5, 'Orange', 'O', '#FFA500', '2016-05-20 23:05:55'),
	(6, 'Purple', 'P', '#A020F0', '2016-05-20 23:06:17'),
	(7, 'Cyan', 'C', '#00FFFF', '2016-05-20 23:06:52'),
	(8, 'Magenta', 'M', '#FF00FF', '2016-05-20 23:07:19');
/*!40000 ALTER TABLE `tb_color` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_game
CREATE TABLE IF NOT EXISTS `tb_game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_key` varchar(100) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solved` enum('Y','N') NOT NULL DEFAULT 'N',
  `id_user_solved` int(11) DEFAULT NULL,
  `dt_solved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_u_game_key` (`game_key`),
  KEY `fk_game_user` (`id_user_solved`),
  CONSTRAINT `fk_game_user` FOREIGN KEY (`id_user_solved`) REFERENCES `tb_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_game: ~4 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_game` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_game` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_game_color
CREATE TABLE IF NOT EXISTS `tb_game_color` (
  `id_game` int(11) NOT NULL,
  `nb_order` int(11) NOT NULL DEFAULT '0',
  `id_color` int(11) NOT NULL,
  PRIMARY KEY (`id_game`,`nb_order`),
  KEY `fk_game_color_color` (`id_color`),
  CONSTRAINT `fk_game_color_color` FOREIGN KEY (`id_color`) REFERENCES `tb_color` (`id`),
  CONSTRAINT `fk_game_color_game` FOREIGN KEY (`id_game`) REFERENCES `tb_game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_game_color: ~272 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_game_color` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_game_color` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_game_user
CREATE TABLE IF NOT EXISTS `tb_game_user` (
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_game`,`id_user`),
  KEY `fk_game_user_user` (`id_user`),
  CONSTRAINT `fk_game_user_game` FOREIGN KEY (`id_game`) REFERENCES `tb_game` (`id`),
  CONSTRAINT `fk_game_user_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_game_user: ~40 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_game_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_game_user` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_guess
CREATE TABLE IF NOT EXISTS `tb_guess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `exact` tinyint(4) NOT NULL,
  `near` tinyint(4) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_guess_game` (`id_game`),
  KEY `fk_guess_user` (`id_user`),
  CONSTRAINT `fk_guess_game` FOREIGN KEY (`id_game`) REFERENCES `tb_game` (`id`),
  CONSTRAINT `fk_guess_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_guess: ~9 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_guess` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_guess` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_guess_color
CREATE TABLE IF NOT EXISTS `tb_guess_color` (
  `id_guess` int(11) NOT NULL,
  `nb_order` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  PRIMARY KEY (`id_guess`,`nb_order`),
  KEY `fk_guess_color_color` (`id_color`),
  CONSTRAINT `fk_guess_color_color` FOREIGN KEY (`id_color`) REFERENCES `tb_color` (`id`),
  CONSTRAINT `fk_guess_color_guess` FOREIGN KEY (`id_guess`) REFERENCES `tb_guess` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_guess_color: ~72 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_guess_color` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_guess_color` ENABLE KEYS */;


-- Copiando estrutura para tabela mastermind_db.tb_user
CREATE TABLE IF NOT EXISTS `tb_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_last_game` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mastermind_db.tb_user: ~6 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
