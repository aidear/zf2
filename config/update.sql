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
