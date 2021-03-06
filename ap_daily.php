<?php
/** .-------------------------------------------------------------------
 * |    Software: []
 * | Description:
 * |        Site: www.jechorn.cn
 * |-------------------------------------------------------------------
 * |      Author: 王志传
 * |      Email : <jechorn@163.com>
 * |  CreateTime: 2017/8/6-11:27
 * | Copyright (c) 2016-2019, www.jechorn.cn. All Rights Reserved.
 * '-------------------------------------------------------------------*/

// ***************************************************
//                     分割线
// ****************************************************

/**  ---------------------------------------------------------
 * |  关于数据库的详细信息配置已经转移到的db.php文件,请移步到该文件进行配置
 * |-------------------------------------------------------------*/
//引入数据库配置文件
include './dist/db.php';

// ***************************************************
//                     分割线
// ****************************************************

/**  ---------------------------------------------------------
 * |  这里是设置你的表字段名称以及数据表的名称
 * |  ps: 数据表的字段请按照csv文件中表头的字段顺序就行设置，要不将会导致数据导入存在问题。
 * |  ps:数据表字段的设置实例 ['statistic_time','ap_mac']
 * |  ps:请注意数组中的逗号为英文字符中逗号,单引号也是英文字符中的单引号,务必注意
 * |-------------------------------------------------------------*/

$tableName = 'ap_daily'; //需要插入的数据表的名称
$dirName = 'daily';   //csv存放的目录，必须在该文件同级的data文件夹里面新建

//数据表的字段名称

/**  ---------------------------------------------------------
 * |  请参考sql文件夹下面对应的创表语句添加数据表的表的字段；
 * |  ps:请按照csv文件第一行的表头字段顺序进行添加数据表的表的字段；
 * |  ps: 比如csv文件第一行的数据为 “统计时间，AP_MAC,地市”，那么相应的表字段就应该设置为：$tableField = ['statistics_time', 'ap_mac', 'city'];
 * |  ps:错误的设置顺序：$tableField = ['statistics_time', 'city','ap_mac'];
 * |-------------------------------------------------------------*/

$tableField = ['statistics_time', 'broadband_account', 'city', 'industry', 'businessman_id', 'businessman_name', 'install_address', 'ap_mac', 'is_match', 'ssid', 'login_page_uv','success_page_uv','login_page_pv','success_page_pv','pv_http','is_active_ap','is_online_ap','portal_lever','process_time','access_number','partner','match_time','ap_version_number','ap_type'];



// ***************************************************
//                     分割线
// ****************************************************

include './dist/import.php';

