<?php
/** .-------------------------------------------------------------------
 * |    Software: []
 * | Description:
 * |        Site: www.jechorn.cn
 * |-------------------------------------------------------------------
 * |      Author: 王志传
 * |      Email : <jechorn@163.com>
 * |  CreateTime: 2017/8/8-16:43
 * | Copyright (c) 2016-2019, www.jechorn.cn. All Rights Reserved.
 * '-------------------------------------------------------------------*/



// 此行文字以下的代码请不要随意修改,切记


set_time_limit(0);
ini_set('memory_limit', '1024M');

setlocale(LC_ALL, 'zh_CN');
error_reporting(E_ALL & ~E_NOTICE);
$dir = dirname(__FILE__) . "/../data/{$dirName}";


//判断文件夹是否存在
if (!is_dir($dir)) {
    $msg = 'Error:directory is not exist';
    echo $msg;
    return false;

}


try {
    $dbh = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true)); //初始化一个PDO对象
    $dbh->exec('set names utf8');
} catch (PDOException $e) {
    die ("Error!: " . $e->getMessage() . "<br/>");
}

//扫描并遍历文件
$fileList = scandir($dir);

$temp = [];

foreach ($fileList as $v) {
    if (pathinfo($v, PATHINFO_EXTENSION) == 'csv') {
        $temp[] = $dir . '/' . $v;
    }
}

//对数据表字段进行组合
$field = '';
$fieldNum = count($tableField) ;
for ($index = 0; $index < $fieldNum; $index++) {
    if ($index == ($fieldNum-1)) {
        $field .= "`{$tableField[$index]}`";
    }else{
        $field .= "`{$tableField[$index]}`,";
    }

}

//每次插入的数据的数量
$size = 10000;
$time = time();
$finalNum = 0;
$finalSuccessNum = 0;
$finalFailNum = 0;
for ($j = 0; $j < count($temp); $j++) {


    $basename = iconv('utf-8','gb2312//ignore',basename($temp[$j]));
    echo iconv('utf-8','gb2312//ignore','正在读取'.$basename.'文件，请稍后......');
    echo PHP_EOL;

    $startTime = time();
    $successNum = 0;
    $failNum = 0;
    //打开文件
    $file = fopen($temp[$j], 'rb');
    $csvData = [];
    //读取里面的内容

    $content = trim(file_get_contents($temp[$j]));
    //匹配内容中的不正常换行符
    //preg_match_all("/\"(.*)(\n*)(.*)(\n*)(.*)\"/iU", $content, $match);
    $content = preg_replace("/\"(.*)(\n*)(.*)(\n*)(.*)\"/iU", '${1}${3}${5}', $content);
    //file_put_contents($temp[$j], $content);


    $csvData = explode(PHP_EOL, $content);

    $csvColumn = count(explode(',', $csvData[0]));
    if ($fieldNum == $csvColumn) {
        unset($csvData[0]);
        $totalNum = count($csvData);
        $finalNum = $finalNum + $totalNum;
        $chunkData = array_chunk($csvData, $size);
        $count = count($chunkData);
        $total = 0;

        for ($k = 0; $k < $count; $k++) {

            $insertRows = [];
            foreach ($chunkData[$k] as $value) {
                //$string = mb_convert_encoding($value, 'GB2312');//转码
                //$string = mb_convert_encoding(trim(strip_tags($value)), 'utf-8','utf-8');//转码
                $string = trim(strip_tags($value));
                $v = explode(',', trim($string));
                $row = [];

                for ($r = 0; $r < $fieldNum; $r++) {
                    //$row[$tableField[$r]] = trim(str_replace("'", "", $v[$r]));
                    $row[$tableField[$r]] = iconv('gb2312','utf-8//ignore',trim(str_replace("'", "", $v[$r])));
                }

                $sqlString = '(' . "'" . implode("','", $row) . "'" . ')'; //批量
                $insertRows[] = $sqlString;


            }
            $data = implode(',', $insertRows);

            $sql = "INSERT IGNORE INTO `{$tableName}` ({$field}) VALUES {$data}";

            $res = $dbh->exec($sql);

            if ($res) {
                $successNum = $successNum + $res;
                echo iconv('utf-8', 'gb2312', '本次插入成功:' . $res . '条,失败0条,总插入成功' . $successNum . '条,总插入失败' . $failNum . '条');
                echo PHP_EOL;
            } else {
                $failNum = $failNum + $size;
                echo iconv('utf-8', 'gb2312', '本次插入成功:0条,失败' . $size . '条,总插入成功' . $successNum . '条,总插入失败' . $failNum . '条');
                echo PHP_EOL;
            }



        }

        fclose($file);
        $endTime = time();
        $thisTime = $endTime - $startTime;
        $finalSuccessNum = $finalSuccessNum + $successNum;
        $finalFailNum = $finalFailNum + $failNum;
        echo iconv('utf-8','gb2312//ignore',$basename.'文件本次执行完毕,用时' . $thisTime . '秒,本应插入' . $totalNum . '条，实际插入' . $successNum . '条,失败' . $failNum . '条');
        echo PHP_EOL;
        $insertRows = null;

    }else{
        echo iconv('utf-8','gb2312//ignore',$basename.'文件跳过执行,文件字段跟数据表字段不对应');
        echo PHP_EOL;
    }

    $content = null;
    $csvData = null;


}
$totalTime = time() - $time;
echo iconv('utf-8', 'gbk', '所有文件执行完毕,共导入' . count($temp) . '个文件,用时' . $totalTime . '秒,本应插入' . $finalNum . '条，实际插入' . $finalSuccessNum . '条,失败' . $finalFailNum . '条');






