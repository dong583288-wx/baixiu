<?php

//公共函数封装
require_once 'config.php';

//打开对应的箱子
session_start();

/*function get_current_user () {

    //那道客户端请求的小票
    //打开对应的箱子,如果没有数据就跳转到登录页面
    if (empty($_SESSION['is_logged_in'])) {

        header('Location: /admin/login.php');

        return;
    }

    return $_SESSION['is_logged_in'];
};*/

function xiu_get_current_user () {
  // 拿到客户端请求带来的小票
  // 找到那个对应箱子，取出标识用户是否登录的数据
  // 根据这个数据判断用户是否登录

  // 如果不存在 is_logged_in 或者值为 false
  if (empty($_SESSION['is_logged_in'])) {
    // 没有登录
    header('Location: /admin/login.php');
    return;
  }
  return $_SESSION['is_logged_in'];
}


/**
 * 简历一个与数据库的链接,返回链接对象,注意要自己关闭链接
 */
function xiu_connect ( ) {
  //建立链接
  $conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
  //判断是否链接失败
  if (!$conn) {
    die('<h1>链接错误(' . mysqli_connect_error() . ')' .mysqli_connect_error() . '</h1>' );
  }

  //链接成功返回这个对象
  return $conn;

}


/**
 * 执行一个sql语句,得到关联数组形式的结果集
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_fetch_all ( $sql ) {

  $conn = xiu_connect();

  //执行数据库查询
  $query = mysqli_query($conn, $sql);

  //查询失败结束函数
  if (!$query) {

    return false;

  }
  //查询成功
  while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
  }
  //$data 查询结果集以关联数组形式的结果集(列表)

  //释放结果集
  mysqli_free_result($query);

  //断开数据库链接
  //数据库链接过多,影响性能

  mysqli_close($conn);

  //返回这个关联数组
  return $data;

}


/**
 * 执行一个sql语句,得到第0条结果
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_fetch_one ($sql) {

  return xiu_fetch_all( $sql )[0];

}


/**
 * 执行一个非查询的查询语句,执行增删改语句
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_execute ($sql) {

  $conn = xiu_connect();

  $query = mysqli_query($conn, $sql);

  if (!$query) {
    return false;
  }
  //获取增删改语句受影响的行数
  $affected_rows = mysqli_affected_rows($conn);

  //增删改语句没有结果集需要释放

  //断开数据库链接
  mysqli_close($conn);

  return $affected_rows;
}
