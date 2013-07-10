/* 
 Список деревьев с колонками:
 ид записи
 Название дерева
 Время создания дерева
 Описание этого дерева
 Тип дерева
 Размер дерва
*/

CREATE TABLE IF NOT EXISTS `#__magictree_list` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `name_tree` varchar(50) NOT NULL,
  `time_tree` datetime NOT NULL,
  `decs_tree` varchar(250) NOT NULL,
  `type_tree` TINYINT  NOT NULL,
  `size_tree` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/* 
 Таблица с типом дерева, подразумевается, что 
 Администратор может создавать разные типы деревьев,
 у каждого дерева может быть свои стиль отображения
 Колонками:
 ид записи
 Название типа дерева
 Цена создания
 Тип цены. rating,karma,billing
*/
CREATE TABLE IF NOT EXISTS `#__magictree_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `cost` int(11) NOT NULL,
  `type_cost` TINYINT NOT NULL,
  `folder_image` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


/* 
  Таблица с группами действий которые могут совершать,
  относительно текущего дерева.
  ид
  Название группы
  К какой группе относится
  Тип цены в группе
*/

CREATE TABLE IF NOT EXISTS `#__magictree_group` (
  `id` int(11) NOT NULL auto_increment,
  `name_gp` varchar(50) NOT NULL,
  `tree_type` TINYINT NOT NULL,
  `type_cost_gp` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


/* 
  Таблица с группами действий которые могут совершать,
  относительно текущего дерева.
  ид
  Название группы
  К какой группе относится
  Тип цены в группе
  На сколько вырастет дерево
  Раз в минуту
*/
CREATE TABLE IF NOT EXISTS `#__magictree_action` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `cost` int(11) NOT NULL,
  `type_cost` TINYINT NOT NULL,
  `action_group` int(11) NOT NULL,
  `rise_tree` int(11) NOT NULL,
  `limit_tree` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/* 
  Таблица с логом действий пользователя
  относительно текущего дерева.
  ид
  Название группы
  К какой группе относится
  Тип цены в группе
  На сколько вырастет дерево
  Раз в минуту
*/

CREATE TABLE IF NOT EXISTS `#__magictree_action_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tree_id` int(11) NOT NULL,
  `end_limit` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;