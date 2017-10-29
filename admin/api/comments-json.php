<?php

//负责返回评论数据的接口
//1.查询数据库中的评论数据
//2.序列化为json
//3.响应给客户端
//4.跳转回主页面
//
//引入数据库链接函数
require_once '../../functions.php';

//处理分页
//没传参数就默认第一页,传了就取
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
//每页条数
$size = 20;
//越过了多少条
$skip = ($page -1) * $size;


//查询总条数
$total_count = (int)xiu_fetch_one('select
  count(1) as i
from comments
inner join posts on comments.post_id = posts.id')['i'];

//总页数
$total_pages = ceil($total_count / $size);




//以下是没有对数据进行分类的
///查询
//data 索引数组套着关联数组
//为了查询效率,可以吧posts表和comments表联合起来,通过post的id和comments的post_id联合
//
//分页功能:让服务端接口一次只返回制定的页数,客户端每次只请求固定的页数
$comments = xiu_fetch_all("select comments.*, posts.title as post_title from comments inner join posts on comments.post_id = posts.id
  order by comments.created desc limit {$skip}, {$size}");
// var_dump($data);
//设置响应数据格式
$json_str = json_encode(array(
  'comments' => $comments,
  'total_pages' => $total_pages
  ));

//设置响应头
header('Content-Type: application/json');

//响应给客户端
 echo $json_str;
