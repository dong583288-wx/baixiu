<?php

//函数封装的文件不能执行两次
require_once '../functions.php';

if (empty($_GET['id'])) {
  //缺少id直接结束
  die('缺少必要的id');
}

$id = $_GET['id'];

//执行mysql的删除语句
xiu_execute('delete from users where id in (' . $id . ');');

//跳转到用户页
header ('Location: /admin/users.php');