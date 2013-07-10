CREATE TABLE IF NOT EXISTS `#__sms_activ` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`user_id` int(11) unsigned DEFAULT NULL COMMENT 'ид пользовател в системе',
	`phone` bigint(20) unsigned NOT NULL COMMENT 'номер абонента',
	`codactiv` int(11) unsigned COMMENT 'код активации',
  PRIMARY KEY  (`id`)
  )ENGINE=MyISAM DEFAULT CHARSET=utf8;