# ************************************************************
# Sequel Pro SQL dump
# Version 4004
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.25a)
# Database: isbt
# Generation Time: 2013-02-25 19:25:42 +0000
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `name`, `level`)
VALUES
	(1,'Ladies Single','A'),
	(2,'Ladies Double','A'),
	(3,'Mens Single','A'),
	(4,'Mens Double','A'),
	(5,'Mix Double','A'),
	(6,'Ladies Single','B'),
	(7,'Ladies Double','B'),
	(8,'Mens Single','B'),
	(9,'Mens Double','B'),
	(10,'Ladies Single','C');

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

LOCK TABLES `court` WRITE;
/*!40000 ALTER TABLE `court` DISABLE KEYS */;

INSERT INTO `court` (`id`, `number`, `locked`)
VALUES
	(1,1,0),
	(2,2,0),
	(3,3,0),
	(4,4,0),
	(5,5,0),
	(6,6,0),
	(7,7,0),
	(8,8,0),
	(9,9,0),
	(10,10,0),
	(11,11,0),
	(12,12,0),
	(13,13,0),
	(14,14,0),
	(15,15,0);

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
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `court` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table monolog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `monolog`;

CREATE TABLE `monolog` (
  `channel` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `message` longtext,
  `time` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table poule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poule`;

CREATE TABLE `poule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `round` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

LOCK TABLES `poule` WRITE;
/*!40000 ALTER TABLE `poule` DISABLE KEYS */;

INSERT INTO `poule` (`id`, `name`, `round`, `category`)
VALUES
	(1,'1',0,1),
	(2,'2',0,2),
	(3,'3',0,3),
	(4,'4',0,4),
	(5,'5',0,5),
	(6,'6',0,6),
	(7,'7',0,7),
	(8,'8',0,8),
	(9,'9',0,9),
	(10,'10',0,10);

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
  `matches_played` int(11) NOT NULL,
  `matches_won` int(11) NOT NULL,
  `matches_lost` int(11) NOT NULL,
  `matches_draw` int(11) NOT NULL,
  `sets_won` int(11) NOT NULL,
  `sets_lost` int(11) NOT NULL,
  `points_won` int(11) NOT NULL,
  `points_lost` int(11) NOT NULL,
  `points_balance` int(11) NOT NULL,
  `average_sets_won` double NOT NULL,
  `IsInOperative` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;

INSERT INTO `team` (`id`, `user1`, `user2`, `poule`, `matches_played`, `matches_won`, `matches_lost`, `matches_draw`, `sets_won`, `sets_lost`, `points_won`, `points_lost`, `points_balance`, `average_sets_won`, `IsInOperative`)
VALUES
	(1,1,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(2,2,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(3,3,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(4,4,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(5,5,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(6,6,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(7,7,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(8,8,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(9,9,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(10,10,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(11,11,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(12,12,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(13,13,NULL,1,0,0,0,0,0,0,0,0,0,0,0),
	(15,14,15,2,0,0,0,0,0,0,0,0,0,0,0),
	(16,16,17,2,0,0,0,0,0,0,0,0,0,0,0),
	(17,18,19,2,0,0,0,0,0,0,0,0,0,0,0),
	(18,20,21,2,0,0,0,0,0,0,0,0,0,0,0),
	(19,22,23,2,0,0,0,0,0,0,0,0,0,0,0),
	(20,24,25,2,0,0,0,0,0,0,0,0,0,0,0),
	(21,26,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(22,27,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(23,28,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(24,29,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(25,30,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(26,31,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(27,32,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(28,33,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(29,34,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(30,35,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(31,36,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(32,37,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(33,38,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(34,39,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(35,40,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(36,41,NULL,3,0,0,0,0,0,0,0,0,0,0,0),
	(37,42,43,4,0,0,0,0,0,0,0,0,0,0,0),
	(38,44,45,4,0,0,0,0,0,0,0,0,0,0,0),
	(39,46,47,4,0,0,0,0,0,0,0,0,0,0,0),
	(40,48,49,4,0,0,0,0,0,0,0,0,0,0,0),
	(41,50,51,4,0,0,0,0,0,0,0,0,0,0,0),
	(42,52,52,4,0,0,0,0,0,0,0,0,0,0,0),
	(43,54,53,4,0,0,0,0,0,0,0,0,0,0,0),
	(44,56,55,4,0,0,0,0,0,0,0,0,0,0,0),
	(45,58,57,4,0,0,0,0,0,0,0,0,0,0,0),
	(46,1,26,5,0,0,0,0,0,0,0,0,0,0,0),
	(47,2,27,5,0,0,0,0,0,0,0,0,0,0,0),
	(48,3,28,5,0,0,0,0,0,0,0,0,0,0,0),
	(49,4,29,5,0,0,0,0,0,0,0,0,0,0,0),
	(50,5,30,5,0,0,0,0,0,0,0,0,0,0,0),
	(51,6,31,5,0,0,0,0,0,0,0,0,0,0,0),
	(52,59,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(53,60,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(54,61,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(55,62,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(56,63,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(57,64,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(58,65,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(59,66,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(60,67,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(61,68,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(62,69,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(63,70,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(64,71,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(65,72,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(66,73,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(67,74,NULL,6,0,0,0,0,0,0,0,0,0,0,0),
	(68,59,69,7,0,0,0,0,0,0,0,0,0,0,0),
	(69,60,70,7,0,0,0,0,0,0,0,0,0,0,0),
	(70,61,71,7,0,0,0,0,0,0,0,0,0,0,0),
	(71,62,72,7,0,0,0,0,0,0,0,0,0,0,0),
	(72,63,73,7,0,0,0,0,0,0,0,0,0,0,0),
	(73,64,74,7,0,0,0,0,0,0,0,0,0,0,0),
	(74,65,75,7,0,0,0,0,0,0,0,0,0,0,0),
	(75,66,76,7,0,0,0,0,0,0,0,0,0,0,0),
	(76,67,77,7,0,0,0,0,0,0,0,0,0,0,0),
	(77,68,78,7,0,0,0,0,0,0,0,0,0,0,0),
	(84,85,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(85,86,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(86,87,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(87,88,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(88,89,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(89,90,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(90,91,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(91,92,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(92,93,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(93,94,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(94,95,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(95,96,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(96,97,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(97,98,NULL,8,0,0,0,0,0,0,0,0,0,0,0),
	(99,98,99,9,0,0,0,0,0,0,0,0,0,0,0),
	(100,97,100,9,0,0,0,0,0,0,0,0,0,0,0),
	(101,96,101,9,0,0,0,0,0,0,0,0,0,0,0),
	(102,95,102,9,0,0,0,0,0,0,0,0,0,0,0),
	(103,94,103,9,0,0,0,0,0,0,0,0,0,0,0),
	(104,93,104,9,0,0,0,0,0,0,0,0,0,0,0),
	(105,92,105,9,0,0,0,0,0,0,0,0,0,0,0),
	(106,91,106,9,0,0,0,0,0,0,0,0,0,0,0),
	(107,107,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(108,108,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(109,109,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(110,110,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(111,111,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(112,112,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(113,113,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(114,114,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(115,115,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(116,116,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(117,117,NULL,10,0,0,0,0,0,0,0,0,0,0,0),
	(118,118,NULL,10,0,0,0,0,0,0,0,0,0,0,0);

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
  `email` varchar(255) DEFAULT NULL,
  `veggie` int(1) NOT NULL,
  `arrival_on_friday` int(1) NOT NULL,
  `diner_on_sunday` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `name`, `club`, `postponed`, `email`, `veggie`, `arrival_on_friday`, `diner_on_sunday`)
VALUES
	(1,'Stanneke',NULL,0,NULL,0,0,1),
	(2,'Charlotte',NULL,0,NULL,0,0,1),
	(3,'Annemarie',NULL,0,NULL,0,0,0),
	(4,'Tess',NULL,0,NULL,0,0,1),
	(5,'Moniek',NULL,0,NULL,1,0,0),
	(6,'Mieke',NULL,0,NULL,0,0,0),
	(7,'Katrien',NULL,0,NULL,0,0,0),
	(8,'Christa',NULL,0,NULL,1,0,0),
	(9,'Lianne',NULL,0,NULL,1,0,1),
	(10,'Anouk',NULL,0,NULL,1,0,1),
	(11,'Ursula',NULL,0,NULL,0,0,0),
	(12,'Wendy',NULL,0,NULL,1,0,0),
	(13,'Erica',NULL,0,NULL,1,0,1),
	(14,'Hilde',NULL,0,NULL,0,0,0),
	(15,'Sandra',NULL,0,NULL,0,1,1),
	(16,'Yvonne',NULL,0,NULL,0,0,0),
	(17,'Paula',NULL,0,NULL,1,0,0),
	(18,'Judy',NULL,0,NULL,0,0,0),
	(19,'Floortje',NULL,0,NULL,0,0,1),
	(20,'Berdien',NULL,0,NULL,0,0,1),
	(21,'Rene',NULL,0,NULL,0,0,0),
	(22,'Manon',NULL,0,NULL,0,0,0),
	(23,'Suus',NULL,0,NULL,0,1,1),
	(24,'Tessa',NULL,0,NULL,1,0,0),
	(25,'Hanneke',NULL,0,NULL,1,0,1),
	(26,'Bart',NULL,0,NULL,1,0,0),
	(27,'Gerben',NULL,0,NULL,0,0,0),
	(28,'Sylvain',NULL,0,NULL,1,0,0),
	(29,'Appie',NULL,0,NULL,0,1,0),
	(30,'Piet-Hein',NULL,0,NULL,0,0,0),
	(31,'Peter',NULL,0,NULL,1,0,1),
	(32,'Rutger',NULL,0,NULL,1,0,1),
	(33,'Joachim',NULL,0,NULL,0,0,0),
	(34,'Rawa Ismael',NULL,0,NULL,0,0,0),
	(35,'Niek',NULL,0,NULL,0,1,0),
	(36,'Dave',NULL,0,NULL,0,0,1),
	(37,'Frank',NULL,0,NULL,1,0,1),
	(38,'Harry',NULL,0,NULL,0,0,0),
	(39,'Dennis',NULL,0,NULL,0,1,0),
	(40,'Jeroen',NULL,0,NULL,0,0,0),
	(41,'Arjan',NULL,0,NULL,0,0,0),
	(42,'Tijn',NULL,0,NULL,0,0,0),
	(43,'Tristan',NULL,0,NULL,0,0,1),
	(44,'Wanno',NULL,0,NULL,0,0,1),
	(45,'Jurjen',NULL,0,NULL,0,0,0),
	(46,'Jeroen',NULL,0,NULL,0,1,0),
	(47,'Vincent',NULL,0,NULL,0,0,0),
	(48,'Michiel',NULL,0,NULL,0,0,0),
	(49,'Eric',NULL,0,NULL,0,0,0),
	(50,'Karl',NULL,0,NULL,0,0,0),
	(51,'Erik',NULL,0,NULL,0,1,1),
	(52,'Raymond',NULL,0,NULL,0,0,1),
	(53,'Wietse',NULL,0,NULL,0,1,0),
	(54,'Quinten',NULL,0,NULL,0,0,1),
	(55,'Chris',NULL,0,NULL,0,0,0),
	(56,'Martin',NULL,0,NULL,0,0,0),
	(57,'Wouter',NULL,0,NULL,0,0,0),
	(58,'Timo',NULL,0,NULL,0,0,0),
	(59,'xStanneke',NULL,0,NULL,0,0,1),
	(60,'xCharlotte',NULL,0,NULL,0,0,1),
	(61,'xAnnemarie',NULL,0,NULL,0,0,0),
	(62,'xTess',NULL,0,NULL,0,0,1),
	(63,'xMoniek',NULL,0,NULL,1,0,0),
	(64,'xMieke',NULL,0,NULL,0,0,0),
	(65,'xKatrien',NULL,0,NULL,0,0,0),
	(66,'xChrista',NULL,0,NULL,1,0,0),
	(67,'xLianne',NULL,0,NULL,1,0,1),
	(68,'xAnouk',NULL,0,NULL,1,0,1),
	(69,'xUrsula',NULL,0,NULL,0,0,0),
	(70,'xWendy',NULL,0,NULL,1,0,0),
	(71,'xErica',NULL,0,NULL,1,0,1),
	(72,'xHilde',NULL,0,NULL,0,0,0),
	(73,'xSandra',NULL,0,NULL,0,1,1),
	(74,'xYvonne',NULL,0,NULL,0,0,0),
	(75,'xPaula',NULL,0,NULL,1,0,0),
	(76,'xJudy',NULL,0,NULL,0,0,0),
	(77,'xFloortje',NULL,0,NULL,0,0,1),
	(78,'xBerdien',NULL,0,NULL,0,0,1),
	(85,'oBart',NULL,0,NULL,1,0,0),
	(86,'oGerben',NULL,0,NULL,0,0,0),
	(87,'oSylvain',NULL,0,NULL,1,0,0),
	(88,'oAppie',NULL,0,NULL,0,1,0),
	(89,'oPiet-Hein',NULL,0,NULL,0,0,0),
	(90,'oPeter',NULL,0,NULL,1,0,1),
	(91,'oRutger',NULL,0,NULL,1,0,1),
	(92,'oJoachim',NULL,0,NULL,0,0,0),
	(93,'oRawa Ismael',NULL,0,NULL,0,0,0),
	(94,'oNiek',NULL,0,NULL,0,1,0),
	(95,'oDave',NULL,0,NULL,0,0,1),
	(96,'oFrank',NULL,0,NULL,1,0,1),
	(97,'oHarry',NULL,0,NULL,0,0,0),
	(98,'oDennis',NULL,0,NULL,0,1,0),
	(99,'oJeroen',NULL,0,NULL,0,0,0),
	(100,'oArjan',NULL,0,NULL,0,0,0),
	(101,'oTijn',NULL,0,NULL,0,0,0),
	(102,'oTristan',NULL,0,NULL,0,0,1),
	(103,'oWanno',NULL,0,NULL,0,0,1),
	(104,'oJurjen',NULL,0,NULL,0,0,0),
	(105,'oJeroen',NULL,0,NULL,0,1,0),
	(106,'oVincent',NULL,0,NULL,0,0,0),
	(107,'Lian',NULL,0,NULL,0,0,0),
	(108,'Lenneke',NULL,0,NULL,0,0,0),
	(109,'Petra',NULL,0,NULL,0,0,0),
	(110,'Vera',NULL,0,NULL,0,0,0),
	(111,'Maria',NULL,0,NULL,0,0,0),
	(112,'Magda',NULL,0,NULL,0,0,0),
	(113,'Evi',NULL,0,NULL,0,0,0),
	(114,'Meike',NULL,0,NULL,0,0,0),
	(115,'Loes',NULL,0,NULL,0,0,0),
	(116,'Anneloes',NULL,0,NULL,0,0,0),
	(117,'Lea',NULL,0,NULL,0,0,0),
	(118,'Lies',NULL,0,NULL,0,0,0);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
