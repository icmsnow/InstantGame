/*
Таблица стастистики:
колличество игр
*/
CREATE TABLE IF NOT EXISTS `#__intuit_stat` (
  `id` int(11) NOT NULL auto_increment,
  `game_count` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
/*
Таблица игр:
ид
создтель
кто в игре
статус 0 - открыта, 1 - 
дата создания игры
победитель
ставка игры
тип ставки в игре 0 - рейтинг 1 карма 2 биллинг
*/
CREATE TABLE IF NOT EXISTS `#__intuit_games` (
  `id` int(11) NOT NULL auto_increment,
  `user_host` int(11) NOT NULL,
  `user_client` int(11) DEFAULT NULL,
  `user_turn` int(11) NOT NULL,
  `x` tinyint(2) NOT NULL,
  `y` tinyint(2) NOT NULL,
  `winner` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `rate` int(11) NOT NULL,
  `type_rate` varchar(3) NOT NULL,
  `date_create` datetime NOT NULL,
  `date_lastaction` datetime DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
/*
Таблица лога действий:
ид
создтель
кто в игре
статус 0 - открыта, 1 - 
дата создания игры
победитель
ставка игры
тип ставки в игре
*/
CREATE TABLE IF NOT EXISTS `#__intuit_games_log`(
  `id` int(11) NOT NULL auto_increment,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `x` tinyint(1) DEFAULT NULL,
  `y` tinyint(1) DEFAULT NULL,
  `date_motion` datetime NOT NULL,
  `user_get` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__intuit_online`(
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `date_motion` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;