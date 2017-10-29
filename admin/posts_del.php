<?php

//载入全部公共函数
require_once '../functions.php';

//判断传参
if (empty($_GET['id'])) {
  die('id没传');
}

$id = $_GET['id'];

//执行数据库删除语句

xiu_execute('delete from posts where id in ('. $id .');' );

//跳转回列表页
//referer的作用是用来标识请求是从哪个页面产生的
//如果直接在浏览器地址栏输入,则没有referer
//可以让图片从哪来回哪去
$referer = $_SERVER['HTTP_REFERER'];

//var_dump($_SERVER);  打印它能找到HTTP_REFERER

//跳转
header('location:' . $referer);