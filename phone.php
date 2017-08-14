<?php
/** .-------------------------------------------------------------------
 * |    Software: []
 * | Description:
 * |        Site: www.jechorn.cn
 * |-------------------------------------------------------------------
 * |      Author: 王志传
 * |      Email : <jechorn@163.com>
 * |  CreateTime: 2017/8/12-14:38
 * | Copyright (c) 2016-2019, www.jechorn.cn. All Rights Reserved.
 * '-------------------------------------------------------------------*/


// ***************************************************
//                     分割线
// ****************************************************

$initTime = time();
require './dist/functions.php';
set_time_limit(0);
ini_set('memory_limit', '1524M');
setlocale(LC_ALL, 'zh_CN');
error_reporting(E_ERROR);

/**  ---------------------------------------------------------
 * |  这里是设置手机号文件和归属地文件的相应信息
 * |  ps: 数据表的字段请按照csv文件中表头的字段顺序就行设置，要不将会导致数据导入存在问题。
 * |  ps:数据表字段的设置实例 ['statistic_time','ap_mac']
 * |  ps:请注意数组中的逗号为英文字符中逗号,单引号也是英文字符中的单引号,务必注意
 * |-------------------------------------------------------------*/

//手机号csv文件和归属地CSV文件的存放目录,务必在data文件夹下面
$subDir = 'phone';

//操作的文件夹,下面一列不用修改
$dir = dirname(__FILE__) . "/data/{$subDir}";   //此列不能修改

//手机号码的csv文件名,该文件只能存在一列，要不出来的结果有误
$phoneName = 'phone.csv';
//号码归属地的csv文件名
//该文件的列头顺序必须是 序号 号段 省份 城市 *;
$locationName = 'location.csv';
//数据输出的文件
$outputName = 'match.csv';


$phoneName = $dir . "/{$phoneName}";
$locationName = $dir . "/{$locationName}";

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


//载入相应的csv文件
$phone = fopen($phoneName, 'r');
$location = fopen($locationName, 'r');
$phoneContent = trim(file_get_contents($phoneName));
$locationContent = trim(file_get_contents($locationName));
fclose($phone);
fclose($location);


//匹配内容中的不正常换行符
$phoneContent = preg_replace("/\"(.*)(\n*)(.*)(\n*)(.*)\"/iU", '${1}${3}${5}', $phoneContent);
$locationContent = preg_replace("/\"(.*)(\n*)(.*)(\n*)(.*)\"/iU", '${1}${3}${5}', $locationContent);

$phoneData = [];


// ***************************************************
//                     分割线
//                手机号的数据预处理
// ****************************************************

//按换行分割csv文件
$phoneData = explode(PHP_EOL, $phoneContent);
$phoneContent = null;
$startTime = time();
echo iconv('utf-8', 'gb2312', '正在清除手机号的异常数据，请稍后');
echo PHP_EOL;
echo iconv('utf-8', 'gb2312', '清理前号码总数' . count($phoneData));
echo PHP_EOL;

//清除异常数据

$phoneData = array_filter($phoneData, function ($var) {
    if ($value = dataFilter($var, 11, '/^1[3|4|5|6|7|8]\d{9}$/i')) {
        return $value;
    }
});
$phoneNum = count($phoneData);
$endTime = time();
echo iconv('utf-8', 'gb2312', '清理后号码总数' . $phoneNum);
echo PHP_EOL;
echo iconv('utf-8', 'gb2312', '清理号码总用时' . ($endTime - $startTime) . '秒');
echo PHP_EOL;

echo iconv('utf-8', 'gb2312', '正在对手机进行升序排序并去重，请稍后');
echo PHP_EOL;
$startTime = time();
$phoneData = array_unique($phoneData);

sort($phoneData);

$endTime = time();
$phoneNum = count($phoneData);
echo iconv('utf-8', 'gb2312', '去重后号码总数' . $phoneNum);
echo PHP_EOL;
echo iconv('utf-8', 'gb2312', '排序号码总用时' . ($endTime - $startTime) . '秒');
echo PHP_EOL;


// ***************************************************
//                     分割线
//                号码归属地的的数据预处理
// ****************************************************

$locationData = [];
// 以换行符分割号码归属地数组
$locationData = explode(PHP_EOL, $locationContent);
$locationContent = null;
unset($locationData[0]);
$startTime = time();
echo iconv('utf-8', 'gb2312', '正在对号码归属地进行升序排序，请稍后');
echo PHP_EOL;
$locationData = arraySequence($locationData, 0);
$endTime = time();
echo iconv('utf-8', 'gb2312', '排序号码归属地总用时' . ($endTime - $startTime) . '秒');
echo PHP_EOL;


// ***************************************************
//                     分割线
//                对手机号码的归属地匹配处理
// ****************************************************

echo iconv('utf-8', 'gb2312', '正在匹配手机号码与号码归属地，请稍后');
echo PHP_EOL;
$startTime = time();
$arr = [];
$index = 0;
$phoneKey = 0;
$count = 0;

foreach ($locationData as $key => $value) {
    $pattern = '/(' . $value[0] . ')([0-9]{4})/i';
    for ($i = $index; $i < $phoneNum; $i++) {
        $arr[$phoneKey]['phone'] = iconv('utf-8', 'gb2312', $phoneData[$i]);

        if (preg_match($pattern, $phoneData[$i], $match) && $value[0] >= mb_substr($phoneData[$i], 0, 7)) {
            $arr[$phoneKey]['province'] = $value[1];
            $arr[$phoneKey]['city'] = $value[2];
            //$arr[$phoneKey]['province'] = iconv('utf-8', 'gb2312', $value[1]);
            //$arr[$phoneKey]['city'] = iconv('utf-8', 'gb2312', $value[2]);
            $phoneKey++;
            $index = $i + 1;
            $count++;
        } elseif (!preg_match($pattern, $phoneData[$i], $match) && $value[0] >= mb_substr($phoneData[$i], 0, 7)) {
            $arr[$phoneKey]['province'] = '';
            $arr[$phoneKey]['city'] = '';
            $phoneKey++;
            $index = $i + 1;
        } else {
            break;
        }


    }

}
$locationData = null;
$endTime = time();
echo iconv('utf-8', 'gb2312', '匹配手机号码与号码归属地完成，耗时' . ($endTime - $startTime) . '秒');
echo PHP_EOL;
echo iconv('utf-8', 'gb2312', '共成功匹配' . $count . '个手机号码');
echo PHP_EOL;

for ($i = $index ;$i < $phoneNum; $i++){
    $arr[$phoneKey]['phone'] = iconv('utf-8', 'gb2312', $phoneData[$i]);
    $arr[$phoneKey]['province'] = '';
    $arr[$phoneKey]['city'] = '';
    $phoneKey++;

}
$phoneData = null;

echo iconv('utf-8', 'gb2312', '正在开始写入内容到'.$outputName.'文件，请稍后');
echo PHP_EOL;
$startTime = time();
$fp = fopen($dir."/".$outputName, 'w') or die(iconv('utf-8', 'gb2312', '错误提示：'.$outputName . '文件已经打开,请关闭文件后重试'));
$phoneNumber = iconv('utf-8', 'gb2312', '手机号码');
$province = iconv('utf-8', 'gb2312', '手机号码');
$city = iconv('utf-8', 'gb2312', '手机号码');
$header = [
    iconv('utf-8', 'gb2312', '手机号码'),
    iconv('utf-8', 'gb2312', '省份'),
    iconv('utf-8', 'gb2312', '城市')
];
array_unshift($arr, $header);

foreach ($arr as $item) {
    fputcsv($fp, $item);
}
fclose($fp);
$endTime = time();
echo iconv('utf-8', 'gb2312', '写入文件完成，耗时' . ($endTime - $startTime) . '秒');
echo PHP_EOL;
echo iconv('utf-8', 'gb2312', '全部过程完成，总耗时' . (time() - $initTime) . '秒');
echo PHP_EOL;











