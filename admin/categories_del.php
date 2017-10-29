<?php
//被载入的文件不能被执行两次

//函数重复载入,导致重名
require_once '../functions.php';

if (empty($_GET['id'])) {
  //缺少必要的id参数
  die('id没传来');
}

$id = $_GET['id'];

//执行删除数据的语句,//id in (xx) 可以删除包含的id
xiu_execute ('delete from categories where id in(' . $id . ');');

//条转回列表页
header('Location: /admin/categories.php');
