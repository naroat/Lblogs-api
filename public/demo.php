<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

function dd($data)
{
    echo "<pre/>";
    var_dump($data);
    exit;
}

//遍历目录
function myScanDir($dir)
{
    $file_arr = [];
    //获取文件和目录
    $scan_arr = scandir($dir);
    foreach ($scan_arr as $val) {
        $full_path = $dir . '/' . $val;
        if ($val != '.' && $val != '..') {
            //验证是否目录，是目录则继续递归
            if (is_dir($full_path)) {
                $file_arr[$full_path] = myScanDir($full_path);
            } else {
                $file_arr[] = $full_path;
            }
        }
    }
    return $file_arr;
}

/*$arr = myScanDir('/Users/taoran/Documents/ranblogs/ranbl-api/app');
echo "<pre/>";
var_dump($arr);
exit;*/

// 无限级分类
$array = array(
    array('id' => 1, 'pid' => 0, 'name' => '河北省'),
    array('id' => 2, 'pid' => 0, 'name' => '北京市'),
    array('id' => 3, 'pid' => 1, 'name' => '邯郸市'),
    array('id' => 4, 'pid' => 2, 'name' => '朝阳区'),
    array('id' => 5, 'pid' => 2, 'name' => '通州区'),
    array('id' => 6, 'pid' => 4, 'name' => '望京'),
    array('id' => 7, 'pid' => 4, 'name' => '酒仙桥'),
    array('id' => 8, 'pid' => 3, 'name' => '永年区'),
    array('id' => 9, 'pid' => 1, 'name' => '武安市'),
);

function getTree($arr)
{
    $new_arr = array_column($arr, null, 'id');
    foreach ($arr as $val) {
        if (isset($arr[$val['pid']])) {
            $new_arr[$val['pid']]['child'][] = $val;
        }
    }
    return $new_arr;
}

/*$new_arr = getTree($array);
dd($new_arr);*/


//鸡兔同笼：今有鸡兔同笼，上有三十五头，下有九十四足，问鸡兔各几何？
/*$chicken = 0;
while ($chicken < 35) {
    $chicken++;
    $rabbit = 35 - $chicken;
    $chicken_foot = 2 * $chicken;
    $rabbit_foot = 4 * $rabbit;
    if ($chicken_foot + $rabbit_foot == 94) {
        echo "鸡有{$chicken}；兔有{$rabbit}";
    }
}*/


//冒泡排序
$arr = [3, 1, 5, 8, 0, 6, 9, 2];
function bubbling($arr) {
    $arr_count = count($arr);
    if ($arr_count <= 1) {
        return $arr;
    }
    for ($i = 0; $i < $arr_count; $i++) {
        for ($j = $i+1; $j < $arr_count; $j++) {
            if ($arr[$i] > $arr[$j]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$i];
                $arr[$i] = $temp;
            }
        }
    }
    return $arr;
}


//快速排序
$arr = [3, 1, 5, 8, 0, 6, 9, 2];
function quickSort($arr)
{
    $arr_count = count($arr);
    if ($arr_count <= 1) {
        return $arr;
    }
    $middle = $arr[0];
    $left = [];
    $right = [];
    for ($i = 1; $i < $arr_count; $i++) {
        if ($middle > $arr[$i]) {
            $left[] = $arr[$i];
        } else {
            $right[] = $arr[$i];
        }
    }
    $left = quickSort($left);
    $right = quickSort($right);
    return array_merge($left, [$middle], $right);

}
dd(quickSort($arr));

//b+tree



