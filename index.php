<?php

header('Content-Type: text/html; charset=utf-8');

require_once('page.php');

$con = require_once('db.php');
$sort_arr = require_once('sort.php');

$page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$cate = intval($_REQUEST['cate']) ? intval($_REQUEST['cate']) : 0;
$sort = intval($_REQUEST['sort']) ? intval($_REQUEST['sort']) : 1;
$sort_info = array_key_exists($sort, $sort_arr) ? $sort_arr[$sort] : $sort_arr[1];

$size = 20;
$start = ($page-1) * $size;

$cates = $con->query("select * from cate")->fetchAll();
if(!$cate) $cate = $cates[0]['id'];
$cates = array_reduce($cates, function($v,$i){$v[$i['id']]=$i['name'];return $v;});
//array_column($cates,'name')
// $cates = array_reduce($cates, create_function('$v,$w', '$v[$w["id"]]=$w["name"];return $v;'));
$catename = $cates[$cate];

$sql = "select * from books where cate = {$cate} order by cast({$sort_info[0]} as DECIMAL(9,2)) desc limit {$start},{$size}";

$list = $con->query($sql)->fetchAll();

$count = $con->query("select count(1) from books where cate = {$cate}")->fetch();
$count = $count[0];
$page = getPage($count,$page,$size);

require_once("list.php");



