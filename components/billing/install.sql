DROP TABLE IF EXISTS `#__billing_psys`;
CREATE TABLE `#__billing_psys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(24) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `config` text NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link` (`link`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__billing_psys` (`id`, `link`, `title`, `url`, `logo`, `config`, `published`, `ordering`) VALUES
(1, 'webmoney', 'WebMoney Transfer', 'http://www.webmoney.ru/', 'logo.gif', '---\ncurrency: \n  WMR: 1\n  WMZ: 30\n  WME: 42\n  WMU: 0.9\nLMI_PAYEE_PURSE_R: \n  title: Кошелек продавца (WMR)\n  value: \nLMI_PAYEE_PURSE_Z: \n  title: Кошелек продавца (WMZ)\n  value: \nLMI_PAYEE_PURSE_E: \n  title: Кошелек продавца (WME)\n  value: \nLMI_PAYEE_PURSE_U: \n  title: Кошелек продавца (WMU)\n  value: \nSECRET_KEY: \n  title: Секретный ключ\n  value: \nLMI_SIM_MODE: \n  title: Режим тестирования (0,1,2)\n  value: 2\nPAYMENT_URL: \n  title: URL для отправки платежа\n  value: >\n    https://merchant.webmoney.ru/lmi/payment.asp\n', 1, 1),
(2, 'robokassa', 'RoboKassa', 'http://www.robokassa.ru/', 'logo.gif', '---\ncurrency: \n  RUR: 1\nsMerchantLogin: \n  title: Логин продавца\n  value: \nsMerchantPass1: \n  title: |\n    Пароль #1\n  value: \nsMerchantPass2: \n  title: |\n    Пароль #2\n  value: \nsCulture: \n  title: Язык интерфейса РобоКассы (en/ru)\n  value: ru\nPAYMENT_URL: \n  title: URL для отправки платежа\n  value: http://test.robokassa.ru/Index.aspx\n', 1, 2),
(3, 'qiwi', 'Qiwi Кошелек', 'https://w.qiwi.ru', 'logo.gif', '---\ncurrency: \n  RUR: 1\nFROM: \n  title: ID продавца\n  value: \nLIFETIME: \n  title: Время жизни счета, час.\n  value: 72\nCHECK_AGT: \n  title: Требовать регистрацию\n  value: 0\nPAYMENT_URL: \n  title: URL для создания счета\n  value: https://w.qiwi.ru/setInetBill.do\n', 1, 3),
(4, 'interkassa', 'Интеркасса', 'http://www.interkassa.com/', 'logo.gif', '---\ncurrency: \n  RUR: 1\nik_shop_id: \n  title: Идентификатор магазина\n  value: \nik_secret_key: \n  title: Секретный ключ\n  value: \nPAYMENT_URL: \n  title: URL для отправки платежа\n  value: >\n    http://www.interkassa.com/lib/payment.php\n', 1, 4);

DROP TABLE IF EXISTS `#__billing_log`;
CREATE TABLE `#__billing_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` tinyint(4) NOT NULL,
  `op_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `summ` float NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `comment` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `op_type` (`op_type`),
  KEY `op_date` (`op_date`),
  KEY `user_id` (`user_id`),
  KEY `owner_id` (`owner_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__billing_actions`;
CREATE TABLE `#__billing_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(25) NOT NULL,
  `action` varchar(25) NOT NULL,
  `title` varchar(250) NOT NULL,
  `point_cost` text NOT NULL,
  `is_free` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `component` (`component`),
  KEY `action` (`action`),
  KEY `is_free` (`is_free`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__billing_subs`;
CREATE TABLE `#__billing_subs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `old_group_id` int(11) NOT NULL,
  `until` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `until` (`until`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__billing_out`;
CREATE TABLE `#__billing_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `amount` float NOT NULL,
  `summ` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `ps_name` varchar(100) NOT NULL,
  `ps_account` varchar(32) NOT NULL,
  `confirm_code` varchar(32) NOT NULL,
  `donedate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `confirm_code` (`confirm_code`),
  KEY `donedate` (`donedate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__billing_tf`;
CREATE TABLE `#__billing_tf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  `confirm_code` varchar(32) NOT NULL,
  `donedate` datetime NOT NULL,
  `comment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`),
  KEY `to_id` (`to_id`),
  KEY `status` (`status`),
  KEY `confirm_code` (`confirm_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

