/*
Navicat MySQL Data Transfer

Source Server         : jechorn
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : data

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-08-08 18:07:55
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for visitor
-- ----------------------------
DROP TABLE IF EXISTS `visitor`;
CREATE TABLE `visitor` (
  `statistics_time`  VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '统计时间段',
  `ap_mac`           VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT 'ap_mac',
  `city`             VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '地市',
  `industry`         VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '行业',
  `businessman_name` VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '商客名称',
  `businessman_id`   VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '商客ID',
  `visitor_account`  VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '访客账号',
  `phone_mac`        VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '终端MAC',
  `phone_type`       VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '终端型号',
  `login_type`       VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '登录方式',
  `login_time`       VARCHAR(255) NOT NULL        DEFAULT ''
  COMMENT '登录时间',
  KEY `city` (`city`),
  KEY `phone_mac` (`phone_mac`),
  KEY `visitor_account` (`visitor_account`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------
-- 全部数据导入数据库成功后执行下面的SQL语句
-- --------------------------------------

ALTER TABLE `visitor`
  ADD INDEX `city` (`city`) ,
  ADD INDEX `phone_mac` (`phone_mac`) ,
  ADD INDEX `visitor_account` (`visitor_account`) ;

-- --------------------------------------
-- 数据批量导入前先禁用索引
-- --------------------------------------
ALTER TABLE `visitor` DISABLE KEYS ;

-- --------------------------------------
-- 数据批量导入成功后再开启索引
-- --------------------------------------
ALTER TABLE `visitor` ENABLE KEYS;



