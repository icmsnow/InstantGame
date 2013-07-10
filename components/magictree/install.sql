/* 
 ������ �������� � ���������:
 �� ������
 �������� ������
 ����� �������� ������
 �������� ����� ������
 ��� ������
 ������ �����
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
 ������� � ����� ������, ���������������, ��� 
 ������������� ����� ��������� ������ ���� ��������,
 � ������� ������ ����� ���� ���� ����� �����������
 ���������:
 �� ������
 �������� ���� ������
 ���� ��������
 ��� ����. rating,karma,billing
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
  ������� � �������� �������� ������� ����� ���������,
  ������������ �������� ������.
  ��
  �������� ������
  � ����� ������ ���������
  ��� ���� � ������
*/

CREATE TABLE IF NOT EXISTS `#__magictree_group` (
  `id` int(11) NOT NULL auto_increment,
  `name_gp` varchar(50) NOT NULL,
  `tree_type` TINYINT NOT NULL,
  `type_cost_gp` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


/* 
  ������� � �������� �������� ������� ����� ���������,
  ������������ �������� ������.
  ��
  �������� ������
  � ����� ������ ���������
  ��� ���� � ������
  �� ������� �������� ������
  ��� � ������
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
  ������� � ����� �������� ������������
  ������������ �������� ������.
  ��
  �������� ������
  � ����� ������ ���������
  ��� ���� � ������
  �� ������� �������� ������
  ��� � ������
*/

CREATE TABLE IF NOT EXISTS `#__magictree_action_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tree_id` int(11) NOT NULL,
  `end_limit` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;