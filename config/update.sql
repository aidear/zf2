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