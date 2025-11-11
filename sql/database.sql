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
  `preparation` varchar(4096) DEFAULT NULL,
  `configure` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `build` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `install` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `localbuild` bit(1) NOT NULL DEFAULT b'0',
  `template` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`release`) USING BTREE,
  KEY `version` (`release`),
  KEY `template_fk` (`template`),
  CONSTRAINT `release_fk` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `template_fk` FOREIGN KEY (`template`) REFERENCES `packages_templates` (`id`) ON UPDATE CASCADE
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
CREATE TABLE `packages_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(10) unsigned NOT NULL,
  `code` varchar(50) CHARACTER SET latin1 NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `configure` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `build` varchar(10240) CHARACTER SET latin1 NOT NULL,
  `install` varchar(10240) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `release_2` (`release`,`code`),
  KEY `release` (`release`),
  KEY `code` (`code`),
  CONSTRAINT `release_fk_package_templates` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `releases_packagessources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `release` int(10) unsigned NOT NULL,
  `source` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `releases_packagessources_fk_release` (`release`),
  CONSTRAINT `releases_packagessources_fk_release` FOREIGN KEY (`release`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `releases_tree` (
  `parent` int(10) unsigned NOT NULL,
  `child` int(10) unsigned NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `releases_tree_fk_child` (`child`),
  CONSTRAINT `releases_tree_fk_child` FOREIGN KEY (`child`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `releases_tree_fk_parent` FOREIGN KEY (`parent`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagessources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagessources_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `source` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`source`),
  CONSTRAINT `packagessources_packages_source` FOREIGN KEY (`source`) REFERENCES `packagessources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
CREATE TABLE `packagessources_packages_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` int(10) unsigned NOT NULL,
  `filename` varchar(100) NOT NULL,
  `link` varchar(1024) NOT NULL,
  `md5` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `packagessources_packages_files_package` (`package`),
  CONSTRAINT `packagessources_packages_files_package` FOREIGN KEY (`package`) REFERENCES `packagessources_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
#SQLDELIMETER
set foreign_key_checks=1;