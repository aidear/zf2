/**
*2013/10/30
*/
ALTER TABLE `project`.`member` ADD COLUMN `address_id` MEDIUMINT(8) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Ĭ���ջ���ַ' AFTER `MSN`;
ALTER TABLE `project`.`address` CHANGE `country` `country` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `province` `province` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `city` `city` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `district` `district` VARCHAR(10) NULL; 
ALTER TABLE `project`.`member` ADD COLUMN `leftMsg` VARCHAR(20) NULL COMMENT 'Ԥ����Ϣ' AFTER `address_id`; 

/**
*2013/10/31
*/
ALTER TABLE `project`.`member` ADD COLUMN `toEmail` VARCHAR(100) NULL COMMENT '��֤�����ַ' AFTER `Email`; 

/**
*2013/11/1
*/
CREATE TABLE `identity_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����id',
  `user_id` int(10) unsigned NOT NULL COMMENT '��Աid',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '��Ա����1-����;2-��ҵ',
  `name` varchar(100) DEFAULT NULL COMMENT '����',
  `code` varchar(100) DEFAULT NULL COMMENT '����',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '״̬0-�����;1-�����ͨ��;2-δͨ�����',
  `addTime` datetime DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `member` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`member` ADD COLUMN `loginProtect` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '�Ƿ�����¼����' AFTER `leftMsg`; 
ALTER TABLE `project`.`member` ADD COLUMN `isValidMobile` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '�Ƿ���֤�ֻ�' AFTER `isValidEmail`; 

/**
*2013/11/2
*/
ALTER TABLE `project`.`member` ADD COLUMN `passwordStrong` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '����ǿ�ȼ���' AFTER `isValidMobile`; 
/**
*2013/11/8
*/
ALTER TABLE `project`.`identity_record` ADD COLUMN `lastApproved` DATETIME NULL COMMENT '������ʱ��' AFTER `status`; 
ALTER TABLE `project`.`member` CHANGE `Points` `Points` VARCHAR(30) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '0' NULL COMMENT '���û���ֵ'; 
CREATE TABLE `secret` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Ψһid',
  `isSelect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '�����Ƿ���ѡ��',
  `content` varchar(200) DEFAULT NULL COMMENT '���⣨���л���',
  `user_id` int(10) unsigned NOT NULL COMMENT '��Աid',
  `addTime` datetime DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `s_user_id` (`user_id`),
  CONSTRAINT `s_user_id` FOREIGN KEY (`user_id`) REFERENCES `member` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8

/**
*2013/11/10
*/
ALTER TABLE `project`.`nav_category` ADD COLUMN `updateTime` DATETIME NULL COMMENT '����ʱ��' AFTER `order`, ADD COLUMN `updateUser` VARCHAR(20) NULL COMMENT '������' AFTER `updateTime`;
ALTER TABLE `project`.`link` ADD COLUMN `updateTime` DATETIME NULL COMMENT '����ʱ��' AFTER `order`, ADD COLUMN `updateUser` VARCHAR(20) NULL COMMENT '������' AFTER `updateTime`; 
ALTER TABLE `project`.`link` ADD COLUMN `recommend_id` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '�Ƽ�id' AFTER `order`; 

CREATE TABLE `recommend_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(20) NOT NULL COMMENT '��ַ����',
  `url` varchar(100) NOT NULL COMMENT '���ӵ�ַ',
  `category` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����',
  `user_name` varchar(80) DEFAULT NULL COMMENT '�Ƽ���',
  `email` varchar(100) DEFAULT NULL COMMENT '����',
  `mobile` varchar(20) DEFAULT NULL COMMENT '�ֻ�',
  `addTime` datetime DEFAULT NULL COMMENT '���ʱ��',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '���״̬(0-����У�1-���ͨ����2-δͨ��)',
  `approvedTime` datetime DEFAULT NULL COMMENT '���ʱ��',
  `approvedUser` varchar(100) DEFAULT NULL COMMENT '�����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
/**
 *11/12/2013
 */
ALTER TABLE `link` ADD INDEX cate(category);
CREATE TABLE `admin_log` (
  `user_id` smallint(5) unsigned NOT NULL COMMENT '�˻�id',
  `user_name` varchar(200) NOT NULL COMMENT '�˻���',
  `opt_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '�������ͣ�0-������1-��¼��2-����',
  `info` varchar(400) DEFAULT '' COMMENT '������Ϣ',
  `ip` varchar(15) DEFAULT '' COMMENT 'ip��ַ',
  `add_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '���ʱ��',
  KEY `usr` (`user_id`,`user_name`),
  KEY `opt_type` (`opt_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/**
 *12/17/2013
 */
CREATE TABLE `project`.`pro_rule_type` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '����',
  `type_name` VARCHAR (60) NOT NULL COMMENT '��������',
  `enable` TINYINT (1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '�Ƿ�����',
  `add_time` DATETIME NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM CHARSET = utf8 COLLATE = utf8_general_ci ;

CREATE TABLE `pro_rule` (
  `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `rule_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '�������',
  `points` varchar(10) NOT NULL DEFAULT '0' COMMENT '����ֵ',
  `start_time` datetime DEFAULT NULL COMMENT '��ʼʱ��',
  `end_time` datetime DEFAULT NULL COMMENT '����ʱ��',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '�Ƿ񼤻�',
  `add_time` datetime NOT NULL COMMENT '���ʱ��',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '����޸�ʱ��',
  PRIMARY KEY (`id`),
  KEY `rule` (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`link` ADD COLUMN `icon` VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'icon' AFTER `category`, ADD COLUMN `show_icon` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '�Ƿ���ʾicon' AFTER `icon`; 

/**
*2013/12/25
*/
ALTER TABLE `project`.`pro_rule_type` CHANGE `id` `type_code` VARCHAR(10) NOT NULL COMMENT 'code';
ALTER TABLE `project`.`pro_rule` CHANGE `rule_id` `rule_code` VARCHAR(20) DEFAULT '0' NOT NULL COMMENT '�������'; 
ALTER TABLE `project`.`pro_rule_type` DROP PRIMARY KEY, ADD UNIQUE INDEX (`type_code`); 
INSERT INTO pro_rule_type
 VALUES ('reg', 'ע��ɹ�', 1, NOW()),
  ('login', '��¼', 1, NOW()),
   ('online', '����ʱ��', 1, NOW()),
    ('view', '���ҳ��', 1, NOW()),
     ('shopping', '���׳ɹ�', 1, NOW());
ALTER TABLE `project`.`pro_rule` ADD COLUMN `updateUser` VARCHAR(60) DEFAULT '' NOT NULL AFTER `last_update`; 

/**
*12/27/2013
*/
CREATE TABLE `point_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `rule_name` text NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `info` text COMMENT '��Դͳ�ƣ���չ��Ϣ',
  `description` text COMMENT '��ϸ��Ϣ',
  `add_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `record_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-���չ��������,2-����Ա��̨���ͻ���,',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`point_history` CHANGE `point` `points` INT(11) DEFAULT 0 NOT NULL;

/**
*2013/12/28
*/
ALTER TABLE `project`.`point_history` ADD COLUMN `rule_id` INT(11) UNSIGNED DEFAULT 0 NOT NULL COMMENT '�id' AFTER `uid`; 
UPDATE member SET Points=0;
ALTER TABLE `project`.`member` CHANGE `Points` `Points` INT(11) UNSIGNED DEFAULT 0 NOT NULL COMMENT '���û���ֵ'; 

/**
*12/30/2013
*/
ALTER TABLE region ADD KEY `type_pid` (`region_type`,`parent_id`);
ALTER TABLE nav_category ADD KEY `show_order` (`isShow`, `order`);
ALTER TABLE region ADD KEY `region_name` (`region_name`);

/**
*2014/1/5
*/
ALTER TABLE `project`.`recommend_link` ADD COLUMN `description` TEXT NULL COMMENT '����' AFTER `mobile`, ADD COLUMN `note` TEXT NULL COMMENT '�������' AFTER `description`; 
CREATE TABLE `adv_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '��վ����',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '��ַ',
  `QQ` varchar(20) NOT NULL DEFAULT '' COMMENT 'QQ',
  `email` varchar(200) NOT NULL DEFAULT '' COMMENT 'email',
  `tel` varchar(200) NOT NULL DEFAULT '' COMMENT '�绰',
  `position` varchar(400) NOT NULL DEFAULT '' COMMENT 'Ͷ��λ��',
  `build_time` varchar(200) NOT NULL DEFAULT '' COMMENT '��վʱ��',
  `dailyView` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�շ�����',
  `summary` text COMMENT '��վ����',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'ip',
  `add_time` datetime DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `content` text COMMENT '����',
  `contact` varchar(200) NOT NULL DEFAULT '' COMMENT '��ϵ��ʽ',
  `ip` char(15) DEFAULT '' COMMENT 'ip',
  `add_time` datetime DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`adv_apply` CHANGE `dailyView` `dailyView` VARCHAR(100) DEFAULT '0' NOT NULL COMMENT '�շ�����'; 

/**
*2014/1/6
*/
ALTER TABLE `project`.`recommend_link` ADD COLUMN `QQ` VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'QQ' AFTER `user_name`; 