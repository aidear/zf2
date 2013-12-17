/**
*2013/10/30
*/
ALTER TABLE `project`.`member` ADD COLUMN `address_id` MEDIUMINT(8) UNSIGNED DEFAULT 0 NOT NULL COMMENT '默认收货地址' AFTER `MSN`;
ALTER TABLE `project`.`address` CHANGE `country` `country` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `province` `province` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `city` `city` VARCHAR(10) DEFAULT '0' NOT NULL, CHANGE `district` `district` VARCHAR(10) NULL; 
ALTER TABLE `project`.`member` ADD COLUMN `leftMsg` VARCHAR(20) NULL COMMENT '预留信息' AFTER `address_id`; 

/**
*2013/10/31
*/
ALTER TABLE `project`.`member` ADD COLUMN `toEmail` VARCHAR(100) NULL COMMENT '验证邮箱地址' AFTER `Email`; 

/**
*2013/11/1
*/
CREATE TABLE `identity_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '会员类型1-个人;2-企业',
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  `code` varchar(100) DEFAULT NULL COMMENT '代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态0-审核中;1-已审核通过;2-未通过审核',
  `addTime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `member` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`member` ADD COLUMN `loginProtect` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '是否开启登录保护' AFTER `leftMsg`; 
ALTER TABLE `project`.`member` ADD COLUMN `isValidMobile` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '是否验证手机' AFTER `isValidEmail`; 

/**
*2013/11/2
*/
ALTER TABLE `project`.`member` ADD COLUMN `passwordStrong` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '密码强度级别' AFTER `isValidMobile`; 
/**
*2013/11/8
*/
ALTER TABLE `project`.`identity_record` ADD COLUMN `lastApproved` DATETIME NULL COMMENT '最后审核时间' AFTER `status`; 
ALTER TABLE `project`.`member` CHANGE `Points` `Points` VARCHAR(30) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '0' NULL COMMENT '可用积分值'; 
CREATE TABLE `secret` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '唯一id',
  `isSelect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '问题是否是选择',
  `content` varchar(200) DEFAULT NULL COMMENT '问题（序列化）',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `addTime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `s_user_id` (`user_id`),
  CONSTRAINT `s_user_id` FOREIGN KEY (`user_id`) REFERENCES `member` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8

/**
*2013/11/10
*/
ALTER TABLE `project`.`nav_category` ADD COLUMN `updateTime` DATETIME NULL COMMENT '更新时间' AFTER `order`, ADD COLUMN `updateUser` VARCHAR(20) NULL COMMENT '更新人' AFTER `updateTime`;
ALTER TABLE `project`.`link` ADD COLUMN `updateTime` DATETIME NULL COMMENT '更新时间' AFTER `order`, ADD COLUMN `updateUser` VARCHAR(20) NULL COMMENT '更新人' AFTER `updateTime`; 
ALTER TABLE `project`.`link` ADD COLUMN `recommend_id` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '推荐id' AFTER `order`; 

CREATE TABLE `recommend_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(20) NOT NULL COMMENT '网址标题',
  `url` varchar(100) NOT NULL COMMENT '链接地址',
  `category` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `user_name` varchar(80) DEFAULT NULL COMMENT '推荐人',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机',
  `addTime` datetime DEFAULT NULL COMMENT '添加时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态(0-审核中，1-审核通过，2-未通过)',
  `approvedTime` datetime DEFAULT NULL COMMENT '审核时间',
  `approvedUser` varchar(100) DEFAULT NULL COMMENT '审核人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
/**
 *11/12/2013
 */
ALTER TABLE `link` ADD INDEX cate(category);
CREATE TABLE `admin_log` (
  `user_id` smallint(5) unsigned NOT NULL COMMENT '账户id',
  `user_name` varchar(200) NOT NULL COMMENT '账户名',
  `opt_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型（0-其他，1-登录，2-管理）',
  `info` varchar(400) DEFAULT '' COMMENT '描述信息',
  `ip` varchar(15) DEFAULT '' COMMENT 'ip地址',
  `add_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  KEY `usr` (`user_id`,`user_name`),
  KEY `opt_type` (`opt_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/**
 *12/17/2013
 */
CREATE TABLE `project`.`pro_rule_type` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增',
  `type_name` VARCHAR (60) NOT NULL COMMENT '规则名称',
  `enable` TINYINT (1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用',
  `add_time` DATETIME NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM CHARSET = utf8 COLLATE = utf8_general_ci ;

CREATE TABLE `pro_rule` (
  `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `rule_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '规则分类',
  `points` varchar(10) NOT NULL DEFAULT '0' COMMENT '积分值',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否激活',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `rule` (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `project`.`link` ADD COLUMN `icon` VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'icon' AFTER `category`, ADD COLUMN `show_icon` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT '是否显示icon' AFTER `icon`; 