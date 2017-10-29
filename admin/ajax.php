<?php

require_once '../config.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';


//链接数据库
$conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);

if (!$conn) {
  die('链接数据库失败');
}

//根据邮箱查询数据库用户信息,limit是为了提高效率

$result = mysqli_query($conn, "select * from users where email = '$email' limit 1;");

if (!$result) {

  $GLOBALS['error_msg'] = '用户名错误';

  exit;
}



//用变量接收查询的对象集
$user = mysqli_fetch_assoc($result);

$avatar = isset($user['avatar']) ? $user['avatar'] : "/static/assets/img/default.png";

echo "$avatar";
