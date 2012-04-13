
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- データベース: `photo_share`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `file_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` smallint(6) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(10) unsigned DEFAULT NULL,
  `user_id` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`file_id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `url`
--

CREATE TABLE IF NOT EXISTS `url` (
  `url_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` smallint(6) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`url_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

