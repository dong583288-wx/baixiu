<?php

//函数封装的文件不能执行两次
require_once '../functions.php';

if (empty($_GET['email'])) {
  //缺少id直接结束
  die('缺少必要的id');
}

$email = $_GET['email'];

$conn = xiu_connect();

$query= mysqli_query($conn,"select * from users where email ='$email'");

$user = mysqli_fetch_assoc($query);

$avatar = isset($user['avatar']) ? $user['avatar'] : '/static/assets/img/default.png';


echo $avatar;

//header ('Location: /admin/users.php');