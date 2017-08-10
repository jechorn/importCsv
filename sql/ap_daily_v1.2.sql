/*
Navicat MySQL Data Transfer

Source Server         : jechorn
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : data

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-08-07 13:20:20
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ap_daily
-- ----------------------------
DROP TABLE IF EXISTS `ap_daily`;
CREATE TABLE `ap_daily` (
  `statistics_time`  VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '统计时间段',
  `broadband_account` VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '宽带账号',
  `city`              VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '地市',
  `industry`          VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '行业',
  `businessman_id`    VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '商客ID',
  `businessman_name`  VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '商客名称',
  `install_address`   VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '装机地址',
  `ap_mac`            VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'ap_mac',
  `is_match`          VARCHAR(225) NOT NULL DEFAULT ''
  COMMENT '是否安审匹配',
  `ssid`              VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'SSID名称',
  `login_page_uv`     VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '登录页UV',
  `success_page_uv`   VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '成功页UV',
  `login_page_pv`     VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '登录页PV',
  `success_page_pv`   VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '成功页pv',
  `pv_http`           VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'pv(http请求)',
  `is_active_ap`      VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '是否活跃AP',
  `is_online_ap`      VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '是否在线AP',
  `portal_lever`      VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'portal档次',
  `process_time`      VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '受理时间',
  `access_number`     VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '接入号',
  `partner`           VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '合作商',
  `match_time`        VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '安审匹配成功时间',
  `ap_version_number` VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'AP版本号',
  `ap_type`           VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT 'AP类别',

  KEY `city` (`city`),
  KEY `portal_lever` (`portal_lever`),
  KEY `broadband_account` (`broadband_account`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
SET FOREIGN_KEY_CHECKS = 1;



-- --------------------------------------
-- 全部数据导入数据库成功后执行下面的SQL语句
-- --------------------------------------

ALTER TABLE `ap_daily`
  ADD INDEX `city` (`city`) ,
  ADD INDEX `portal_lever` (`portal_lever`) ,
  ADD INDEX `broadband_account` (`broadband_account`) ;

-- --------------------------------------
-- 数据批量导入前先禁用索引
-- --------------------------------------
ALTER TABLE `ap_daily` DISABLE KEYS ;

-- --------------------------------------
-- 数据批量导入成功后再开启索引
-- --------------------------------------
ALTER TABLE `ap_daily` ENABLE KEYS;



-- -----------------------------
-- 优化后的宽带账号去重视图
-- version:v1.3
-- Created:2017-08-02
-- ----------------------------
DROP VIEW IF EXISTS `portal_sort`;
CREATE VIEW `portal_sort` AS
  SELECT
    `broadband_account`,
    #LEFT(`portal`,(LENGTH(`portal`)-3))
    REVERSE(SUBSTR(REVERSE(`portal_lever`), 4, LENGTH(`portal_lever`)))
      AS `portal_value`,
    `city`
  FROM `ap_daily`
  WHERE `broadband_account` NOT IN (SELECT `broadband_account`
                                   FROM `ap_daily`
                                   GROUP BY `broadband_account`
                                   HAVING COUNT(*) > 1)
  UNION

  SELECT DISTINCT
    aaa.`broadband_account`,
    aaa.`portal_value`,
    bbb.`city`
  FROM (
         SELECT
           `broadband_account`,
           MAX(REVERSE(SUBSTR(REVERSE(`portal_lever`), 4, LENGTH(`portal_lever`)))) AS `portal_value`
         FROM `ap_daily`
         GROUP BY `broadband_account`
         HAVING COUNT(*) > 1
       ) AS aaa
    INNER JOIN (
                 SELECT
                   `broadband_account`,
                   REVERSE(SUBSTR(REVERSE(`portal_lever`), 4, LENGTH(`portal_lever`))) AS `portal_value`,
                   `city`
                 FROM `ap_daily`
               ) AS bbb
      ON aaa.`broadband_account` = bbb.`broadband_account` AND aaa.`portal_value` = bbb.`portal_value`;

-- -----------------------------
-- 优化后的宽带账号去重
-- 统计各地区每个portal档次的人数
-- time: 2017-08-07
-- -----------------------------


SELECT
  aa.*,
  IFNULL(bb.`portal_0`, 0)  AS `portal_0`,
  IFNULL(cc.`portal_10`, 0) AS `portal_10`,
  IFNULL(dd.`portal_30`, 0) AS `portal_30`,
  IFNULL(ee.`portal_50`, 0) AS `portal_50`
FROM (
       SELECT
         a.`city`,
         COUNT(*) AS `total`
       FROM `portal_sort` AS a
       GROUP BY a.`city`
     ) AS aa
  LEFT JOIN (
              SELECT
                b.`city`,
                COUNT(*) AS `portal_0`
              FROM (SELECT
                      `city`,
                      `portal_value`
                    FROM `portal_sort` AS aaaa
                    WHERE `portal_value` = 0) AS b
              GROUP BY b.`city`
            ) AS bb ON aa.`city` = bb.`city`
  LEFT JOIN (
              SELECT
                c.`city`,
                COUNT(*) AS `portal_10`
              FROM (SELECT
                      `city`,
                      `portal_value`
                    FROM `portal_sort` AS aaaa
                    WHERE `portal_value` = 10) AS c
              GROUP BY c.`city`
            ) AS cc ON aa.`city` = cc.`city`
  LEFT JOIN (
              SELECT
                d.`city`,
                COUNT(*) AS `portal_30`
              FROM (SELECT
                      `city`,
                      `portal_value`
                    FROM `portal_sort` AS aaaa
                    WHERE `portal_value` = 30) AS d
              GROUP BY d.`city`
            ) AS dd ON aa.`city` = dd.`city`
  LEFT JOIN (
              SELECT
                e.`city`,
                COUNT(*) AS `portal_50`
              FROM (SELECT
                      `city`,
                      `portal_value`
                    FROM `portal_sort` AS aaaa
                    WHERE `portal_value` = 50) AS e
              GROUP BY e.`city`
            ) AS ee ON aa.`city` = ee.`city` ORDER BY aa.`total` DESC;

