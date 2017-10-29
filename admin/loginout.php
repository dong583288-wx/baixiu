<?php

session_start();

//找到钥匙对应的箱子
//删除这个箱子里用来表示用户登录状态的数据(钥匙)

unset($_SESSION['is_logged_in']);

//跳转到登录页面
header('Location: /admin/login.php');
