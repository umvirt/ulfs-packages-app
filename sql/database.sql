set foreign_key_checks=0;
#SQLDELIMETER
CREATE TABLE `architectures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `architectures_packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `architecture` int(10) unsigned NOT NULL,
  `package` bigint(20) unsigned NOT NULL,
  `configure` varchar(10240) CHARACTER SET utf8 NOT NULL,
  `build` varchar(1024) CHARACTER SET utf8 NOT NULL,
  `install` varchar(1024) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `architecture_package` (`architecture`,`package`) USING BTREE,
  KEY `package` (`package`),
  CONSTRAINT `architectures_packages_ibfk_1` FOREIGN KEY (`architecture`) REFERENCES `architectures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `architectures_packages_ibfk_2` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `architectures_dependances` (
  `package` bigint(20) unsigned NOT NULL,
  `dependance` bigint(20) unsigned NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package`,`dependance`),
  KEY `dependance` (`dependance`),
  CONSTRAINT `architectures_dependances_ibfk_1` FOREIGN KEY (`package`) REFERENCES `architectures_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `architectures_dependances_ibfk_2` FOREIGN KEY (`dependance`) REFERENCES `architectures_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
#SQLDELIMETER
CREATE TABLE `releases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `release` varchar(10) CHARACTER SET latin1 NOT NULL,
  `commit` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `release` (`release`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` varchar(1023) NOT NULL,
  `release` int(10) unsigned NOT NULL,
  `sourcefile` varchar(255) CHARACTER SET latin1 NOT NULL,
  `sourcedir` varchar(255) CHARACTER SET latin1 NOT NULL,
  `unpack` varchar(1024) NOT NULL,
  `configure` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `build` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `install` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `localbuild` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`release`) USING BTREE,
  KEY `version` (`release`),
  CONSTRAINT `release_fk` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `patches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) CHARACTER SET latin1 NOT NULL,
  `mode` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `patch_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `addons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `addon_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `dependances` (
  `package` bigint(20) unsigned NOT NULL,
  `dependance` bigint(20) unsigned NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package`,`dependance`),
  KEY `dependances_fk_dependance` (`dependance`),
  CONSTRAINT `dependances_fk_dependance` FOREIGN KEY (`dependance`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dependances_fk_package` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `commands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `commands` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `info` varchar(1024) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `release` (`release`),
  CONSTRAINT `commands_fk_release` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package` bigint(20) unsigned NOT NULL,
  `text` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `comments_fk_packages` FOREIGN KEY (`package`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `nestings` (
  `parent` bigint(20) unsigned NOT NULL,
  `child` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `nestings_ibfk_2` (`child`),
  CONSTRAINT `nestings_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nestings_ibfk_2` FOREIGN KEY (`child`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagespatchesfiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(10) unsigned NOT NULL,
  `filename` varchar(255) CHARACTER SET latin1 NOT NULL,
  `path` varchar(255) NOT NULL,
  `mtime` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `md5_current` varchar(255) CHARACTER SET latin1 NOT NULL,
  `md5_stored` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagesfiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(10) unsigned NOT NULL,
  `filename` varchar(255) CHARACTER SET latin1 NOT NULL,
  `path` varchar(255) NOT NULL,
  `mtime` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `md5_current` varchar(255) CHARACTER SET latin1 NOT NULL,
  `md5_stored` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagesfiles_packages` (
  `packagefile` int(11) unsigned NOT NULL,
  `package` int(11) unsigned NOT NULL,
  PRIMARY KEY (`packagefile`,`package`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
#SQLDELIMETER
set foreign_key_checks=1;