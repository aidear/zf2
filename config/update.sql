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
