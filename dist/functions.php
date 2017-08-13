<?php
/** .-------------------------------------------------------------------
 * |    Software: []
 * | Description:
 * |        Site: www.jechorn.cn
 * |-------------------------------------------------------------------
 * |      Author: 王志传
 * |      Email : <jechorn@163.com>
 * |  CreateTime: 2017/8/13-4:03
 * | Copyright (c) 2016-2019, www.jechorn.cn. All Rights Reserved.
 * '-------------------------------------------------------------------*/


// ***************************************************
//                     分割线
// ****************************************************




/**
 * 根据字段进行排序
 * @param array  $array  需要排序的数组
 * @param string $field  排序的字段
 * @param string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 * @return array 重新排序后的数组
 */
function arraySequence($array, $field, $sort = 'SORT_ASC')
{
    $arrSort = [];
    $arr = [];
    foreach ($array as $uniqid => $row) {

        $row = explode(',', $row);
        $arr[] = $row;
        foreach ($row as $key => $value) {

            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $arr);
    return $arr;
}



// ***************************************************
//                     分割线
// ****************************************************


/**
 * @param string $value 需要检测的数组元素
 * @param int  $length  排序的字段
 * @param string $pattern 正则匹配的模式
 * @return mixed 匹配成功则返回素组元素
 */
function dataFilter($value,$length,$pattern)
{
    if (is_numeric($value) && strlen($value) == $length && preg_match($pattern, $value, $match)) {
        return $value;
    }
    return false;
}