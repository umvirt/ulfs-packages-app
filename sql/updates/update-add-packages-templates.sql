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

alter table `packages` add column `template` int(10) unsigned DEFAULT NULL;

alter table `packages` add KEY `template_fk` (`template`);

alter table `packages` add CONSTRAINT `template_fk` FOREIGN KEY (`template`) REFERENCES `packages_templates` (`id`) ON UPDATE CASCADE;

