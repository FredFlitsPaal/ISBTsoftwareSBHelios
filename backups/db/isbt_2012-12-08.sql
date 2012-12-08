# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.25a)
# Database: isbt
# Generation Time: 2012-12-08 18:48:06 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `level` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `name`, `level`)
VALUES
	(1,'Heren Enkel','A'),
	(2,'Dames Enkel','A'),
	(3,'Heren Dubbel','A'),
	(4,'Dames Dubbel','A'),
	(5,'Gemengd Dubbel','A');

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table court
# ------------------------------------------------------------

DROP TABLE IF EXISTS `court`;

CREATE TABLE `court` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `locked` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `court` WRITE;
/*!40000 ALTER TABLE `court` DISABLE KEYS */;

INSERT INTO `court` (`id`, `number`, `locked`)
VALUES
	(1,1,0),
	(2,2,0),
	(3,3,0);

/*!40000 ALTER TABLE `court` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table match
# ------------------------------------------------------------

DROP TABLE IF EXISTS `match`;

CREATE TABLE `match` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `team1` int(11) NOT NULL,
  `team2` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `team1_set1_score` int(11) DEFAULT NULL,
  `team1_set2_score` int(11) DEFAULT NULL,
  `team1_set3_score` int(11) DEFAULT NULL,
  `team2_set1_score` int(11) DEFAULT NULL,
  `team2_set2_score` int(11) DEFAULT NULL,
  `team2_set3_score` int(11) DEFAULT NULL,
  `comment` varchar(30) DEFAULT '',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `court` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `match` WRITE;
/*!40000 ALTER TABLE `match` DISABLE KEYS */;

INSERT INTO `match` (`id`, `team1`, `team2`, `round`, `status`, `team1_set1_score`, `team1_set2_score`, `team1_set3_score`, `team2_set1_score`, `team2_set2_score`, `team2_set3_score`, `comment`, `start_time`, `end_time`, `court`)
VALUES
	(1,1,2,1,0,21,10,21,10,21,20,'Test comment',NULL,NULL,NULL);

/*!40000 ALTER TABLE `match` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table poule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poule`;

CREATE TABLE `poule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `round` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `matches_played` int(11) NOT NULL,
  `matches_won` int(11) NOT NULL,
  `matches_lost` int(11) NOT NULL,
  `matches_draw` int(11) NOT NULL,
  `sets_won` int(11) NOT NULL,
  `sets_lost` int(11) NOT NULL,
  `points_won` int(11) NOT NULL,
  `points_lost` int(11) NOT NULL,
  `points_balance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `poule` WRITE;
/*!40000 ALTER TABLE `poule` DISABLE KEYS */;

INSERT INTO `poule` (`id`, `name`, `round`, `category`, `matches_played`, `matches_won`, `matches_lost`, `matches_draw`, `sets_won`, `sets_lost`, `points_won`, `points_lost`, `points_balance`)
VALUES
	(1,'1',1,1,0,0,0,0,0,0,0,0,0);

/*!40000 ALTER TABLE `poule` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table team
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team`;

CREATE TABLE `team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user1` int(11) NOT NULL,
  `user2` int(11) DEFAULT NULL,
  `poule` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;

INSERT INTO `team` (`id`, `user1`, `user2`, `poule`)
VALUES
	(1,1,2,1),
	(2,3,3,1);

/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `club` varchar(255) DEFAULT NULL,
  `postponed` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `name`, `club`, `postponed`)
VALUES
	(1,'Leon Bunschoten','SB Helios',0),
	(2,'Frederik Leenders','SB Helios',0),
	(3,'Wouter van Dijk','SB Helios',0);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
