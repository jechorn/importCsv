<?php
/** .-------------------------------------------------------------------
 * |    Software: []
 * | Description:
 * |        Site: www.jechorn.cn
 * |-------------------------------------------------------------------
 * |      Author: 王志传
 * |      Email : <jechorn@163.com>
 * |  CreateTime: 2017/8/13-14:54
 * | Copyright (c) 2016-2019, www.jechorn.cn. All Rights Reserved.
 * '-------------------------------------------------------------------*/

//引入数据库配置文件
include './dist/db.php';

//数据输出的文件
$outputName = 'type.csv';

//导出的csv文件的存放目录,务必在data文件夹下面
$subDir = 'type';

//操作的文件夹,下面一列不用修改
$dir = dirname(__FILE__) . "/data/{$subDir}";   //此列不能修改


// ***************************************************
//                     分割线
//               请不要修改下面的内容
// ****************************************************

if (!is_dir($dir)) {
    echo iconv('utf-8', 'gb2312', $subDir . '文件夹不存在，请在' . dirname(__FILE__) . "/data文件夹下创建");
    echo PHP_EOL;
    return;
}
if (!is_writeable($dir)) {
    echo iconv('utf-8', 'gb2312', $subDir . '文件夹不可写，请修改' . $dir. "文件夹权限");
    echo PHP_EOL;
    return;

}



// ***************************************************
//                     分割线
// ****************************************************

set_time_limit(0);
ini_set('memory_limit', '1024M');

setlocale(LC_ALL, 'zh_CN');
error_reporting(E_ALL & ~E_NOTICE);

$initTime = time();
//链接数据库
try {
    $dbh = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true)); //初始化一个PDO对象
    $dbh->exec('set names utf8');
} catch (PDOException $e) {
    die ("Error!: " . $e->getMessage() . "<br/>");
}

echo iconv('utf-8', 'gb2312', '正在查询数据库信息,请稍后');
echo PHP_EOL;
$startTime = time();
$sql_1 = 'SELECT `login_type`,COUNT(*) AS total FROM `visitor` GROUP BY `login_type`;';
$res = $dbh->query($sql_1);
if ($row = $res->fetchAll(PDO::FETCH_ASSOC)){
    $list = $row;


    foreach ($list as $k=> $v) {


        $sql_2 = "SELECT COUNT(a.`visitor_account`) AS total_phone FROM (SELECT `visitor_account` FROM `visitor` WHERE `login_type` ={$v['login_type']} GROUP BY `visitor_account`) AS a";
        if (empty($v['login_type'])){
            $sql_2 = "SELECT COUNT(a.`visitor_account`) AS total_phone FROM (SELECT `visitor_account` FROM `visitor` WHERE `login_type` ='' GROUP BY `visitor_account`) AS a";
        }

        $res = $dbh->prepare($sql_2);
        $res->execute();
        if($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $list[$k]['total_phone'] = $row['total_phone'];

        }else{
            $list[$k]['total_phone'] = 0;
        }

    }
    $endTime = time();
    echo iconv('utf-8', 'gb2312', '数据查询结束，共耗时'.($endTime-$startTime).'秒');
    echo PHP_EOL;

    echo iconv('utf-8', 'gb2312', '正在开始写入内容到'.$outputName.'文件，请稍后......');
    echo PHP_EOL;
    $startTime = time();
    $fp = fopen($dir."/".$outputName, 'w') or die(iconv('utf-8', 'gb2312', '错误提示：'.$outputName . '文件已经打开,请关闭文件后重试'));
    $loginType = iconv('utf-8', 'gb2312', '登录方式');
    $total = iconv('utf-8', 'gb2312', '总登录次数');
    $phoneTotal = iconv('utf-8', 'gb2312', '手机号总数');
    $header = [
        iconv('utf-8', 'gb2312', '登录方式'),
        iconv('utf-8', 'gb2312', '总登录次数'),
        iconv('utf-8', 'gb2312', '手机号总数')
    ];
    array_unshift($list, $header);
    foreach ($list as $item) {
        fputcsv($fp, $item);
    }
    fclose($fp);
    $endTime = time();

    echo iconv('utf-8', 'gb2312', '写入文件完成，耗时' . ($endTime - $startTime) . '秒');
    echo PHP_EOL;
    echo iconv('utf-8', 'gb2312', '全部过程完成，总耗时' . (time() - $initTime) . '秒');
    echo PHP_EOL;



}


