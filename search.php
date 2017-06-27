<?php
//搜索具体书名
header('Content-Type: text/html; charset=utf-8');
require_once('page.php');

$name = $_REQUEST['name'];
if(!$name) {
	echo '<script>history.back()</script>';
	exit();
}

$sort_arr = require_once('sort.php');

$page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$sort = intval($_REQUEST['sort']) ? intval($_REQUEST['sort']) : 1;
$sort_info = array_key_exists($sort, $sort_arr) ? $sort_arr[$sort] : $sort_arr[1];

$con = require_once('db.php');

$size = 20;
$start = ($page-1) * $size;
$list = $con->query("select b.*,c.name as catename from books as b left join cate as c on b.cate = c.id where title like '%{$name}%' order by cast({$sort_info[0]} as DECIMAL(9,2)) desc limit {$start},{$size}");

$list = $list ? $list->fetchAll() : [];

$count = $con->query("select count(1) from books where title like '%{$name}%'");

$count = $count ? $count->fetch()[0] : 0;

if($count > 0)
	$page = getPage($count,$page,$size);
else{
	$page = "<h2>未找到指定书目-{$name}</h2>";
}
require_once("list.php");


