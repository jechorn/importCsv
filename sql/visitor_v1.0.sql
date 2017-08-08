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
