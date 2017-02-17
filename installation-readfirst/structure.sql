CREATE TABLE IF NOT EXISTS `cronjob` (
  `status` varchar(255) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `email` (
  `project_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('b','s','u') NOT NULL DEFAULT 'u',
  `times` bigint(20) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `project` (`project_id`,`email`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL,
  `crawled_urls` bigint(20) unsigned NOT NULL,
  `crawled_failed` bigint(20) unsigned NOT NULL,
  `crawled_emails` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `project_daily_stats` (
  `project_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `project_id` (`project_id`,`date`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `project_stats` (
  `project_id` int(10) unsigned NOT NULL,
  `type` enum('p','c') NOT NULL DEFAULT 'c',
  `status` varchar(20) NOT NULL,
  `number` bigint(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `project_id` (`project_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `spider` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `ref_id` bigint(20) unsigned NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `failed` tinyint(4) NOT NULL DEFAULT '0',
  `failed_id` tinyint(3) unsigned NOT NULL,
  `failed_msg` varchar(255) NOT NULL,
  `links` int(10) unsigned NOT NULL,
  `emails` int(10) unsigned NOT NULL,
  `times` bigint(20) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`url`),
  KEY `failed` (`failed`),
  KEY `project_id` (`project_id`,`processed`),
  KEY `failed_2` (`failed`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `validation` varchar(128) NOT NULL,
  `username` varchar(20) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `createdate` datetime NOT NULL,
  `date_lastlogin` datetime NOT NULL,
  `ip_lastlogin` varchar(50) NOT NULL,
  `ip_registration` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
