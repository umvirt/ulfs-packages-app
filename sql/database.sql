set foreign_key_checks=0;
#SQLDELIMETER
CREATE TABLE `releases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `release` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `release` int(10) unsigned NOT NULL,
  `sourcefile` varchar(255) NOT NULL,
  `sourcedir` varchar(255) NOT NULL,
  `configure` varchar(10240) NOT NULL,
  `build` varchar(10240) NOT NULL,
  `install` varchar(10240) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`release`) USING BTREE,
  KEY `version` (`release`),
  CONSTRAINT `release_fk` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=361 DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `patches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `patch_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `addons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `addon_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `dependances` (
  `package` bigint(20) unsigned NOT NULL,
  `dependance` bigint(20) unsigned NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package`,`dependance`),
  KEY `dependances_fk_dependance` (`dependance`),
  CONSTRAINT `dependances_fk_dependance` FOREIGN KEY (`dependance`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dependances_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `commands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `commands` varchar(10240) NOT NULL,
  `info` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `release` (`release`),
  CONSTRAINT `commands_fk_release` FOREIGN KEY (`release`) REFERENCES `releases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
#SQLDELIMETER
set foreign_key_checks=1;