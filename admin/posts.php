<?php

/*
分类筛选功能:
1. 获取所有分类的数据
2. 在下拉菜单中foaerch出获取的分类数据
3. 客户端选择了某个分类,点击筛选,可以选出那一个分类的所有数据,所以服务端发送一个数据过去告诉服务端,选了哪个分类.方案是吧当前选择的分类id赋值给value,发送给服务端的就是分类的id,服务端根据id,返回给客户端相应的数据
4. 给下拉框的value赋值分类的id,提交的所选中的行原有的id相同,则表示这行为当前行,在吧selected属性加上.
5.为了提高查询效率,可以在联合查询的筛选后拼where字符串.提交了id才帅选,否则拿全部数据
*/


/*
状态筛选功能
1.只有客户端传递了一个不为all的值,才需要筛选
2.否则就就拿所有的数据
3.数据库where后面的拼接语句要注意空格,利用and来添加多种查询条件
*/



/*
分页功能
1.分页排序默认是按照id排序的,但是实际应该按照时间排序,order by created 以创建时间排序,默认是升序(事件越早排在前面). order by created desc 是降序
2.找出规律0  10  20  30  40
          1  2   3   4   5
规律:(n-1) * 10   (10条数)

*/






//载入全部公共函数
require_once '../functions.php';
//判断是否登录
xiu_get_current_user();



//先拿到posts表中的所有数据(语句要加引号)
//下拉框筛选提交了id,才需要在数据库筛选,否则拿全部数据
//1=1是无意义的相当于没有加,目的是为了查询语句的完整性
//and可以吧两个筛选条件相结合起来
$where = '1=1';
if (isset($_GET['category']) && $_GET['category'] !== 'all' ) {
  //提交了id
  $where .= ' and posts.category_id= ' . $_GET['category'];
}


//客户端传递了一个不为all的值才需要筛选,
//注意:状态值是一个字符串,所必须要用引号,加转义符
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  //提交了id
  $where .= ' and posts.status= \'' . $_GET['status'] . '\'';
}



// ======处理分页参数
//页码,如果没传参,就取第一页
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
//每页条数
$size = 20;
//每次越过的条数
$offset = ($page-1) * $size;


// 查询数据库中满足条件的有多少数据
$total_count = (int)xiu_fetch_one('select
  count(1) as num
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where ' . $where)['num'];


// 根据总条数算出总页数
$total_page = (int)ceil($total_count / $size);



//主官认为开始数就是当前页码-2
$begin = $page - 2 < 1 ? 1 : $page -2;
//主观认为每页结束数就是当前页+4,但是要考虑超出,如果超出总页数,就等于总页数
$end = $begin + 4 ;
if ($end>$total_page) {
  $end = $total_page;
  //end发生了变化,begin也要变化
  $begin = $end - 4 < 1 ? 1: $end -4;
}


//分页页码
//分页数为奇数a,取中间值
//begin为a-2,end为a+2
// $begin = $page - 2;  //大于0的数
// //$end = $page + 2;   //小于总页数
// if ($begin < 1) {
//   $begin = 1;
// }

// $end = $begin + 4;




//字符串拼接echo '\'' . $_GET['status'] . '\'';

//但是下面的$where会覆盖上面的$where,所以还需要处理


$posts = xiu_fetch_all('select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where ' . $where . '
order by posts.created desc
limit ' . $offset . ', ' . $size . ';');

/**
 * 讲英文状态描述转中文
 * @param  [type] $status [description]
 * @return [type]         [description]
 */
function convert_status ($status) {
  /*switch ($status) {

    case 'drafted':
      return '草稿';

  case 'published':
      return '已发布';

  case 'trashed':
      return '回收站';
  default:
      return '未知';
  }*/

  //用关联数组的方式
  $dict = array(
    'drafted' => '草稿',
    'published' => '已发布',
    'trashed' => '回收站'
  );

  return isset($dict[$status]) ? $dict[$status] : '未知';
}


function convert_date ($date) {
  //转换为时间戳
  $timestamp = strtotime($date);
  //由于r在事件格式中有特殊含义,所以要原封不动的表示一个r,需要转义一下
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}


/**
 * 获取用户名
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
/*function get_user_name ($user_id) {
  //获取单行用户名
  $res = xiu_fetch_one('select nickname from users where id = '. $user_id .' limit 1');
    return $res['nickname'];
}*/

 /* 获取单行分类名
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
/*function get_category ($category_id) {
  //获取单行用户名
  $res = xiu_fetch_one('select categories from users where id = '. $category_id .' limit 1');
    return $res['nickname'];
}*/


//取出分类表中的所有数据
$categories = xiu_fetch_all('select * from categories');

//设置cookie
setcookie('page', $_GET['status'], time()+24*3600)


?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include './inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>

            <?php foreach ($categories as $item): ?>
               <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>

          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
          </select>

          <button type="submit" class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="/admin/posts.php?page=<?php echo $page - 1 < 1 ? 1 : $page - 1; ?>">上一页</a></li>
            <?php for ( $i = $begin; $i <= $end; $i++): ?>
              <li <?php echo $page===$i ? 'class="active" ' : '' ?>>
                <a href="/admin/posts.php?page=<?php echo $i ?>">
                  <?php echo $i ?>
                </a>
              </li>
            <?php endfor ?>
          <li><a href="/admin/posts.php?page=<?php echo $page + 1 > $total_page ? $total_page :$page + 1; ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($posts as $value): ?>
            <tr>
            <td class="text-center"><input type="checkbox"></td>

            <td><?php echo $value['title'] ?></td>

            <td><?php echo $value['user_name'] ?></td>

            <td><?php echo $value['category_name'] ?></td>

            <td class="text-center"><?php echo convert_date($value['created']) ?></td>

            <td class="text-center"><?php echo convert_status($value['status']) ?></td>

            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a id="a-del" href="/admin/posts_del.php?id=<?php echo $value['id'] ?>" class="btn btn-danger btn-xs">删除</a>
            </td>

          </tr>
          <?php endforeach ?>

        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <!-- js-cookie-2.1.4.min.js引入 -->
  <script src="/static/assets/vendors/js-cookie/js-cookie-2.1.4.min.js"></script>

  <script>
    //设置cookie
    $(function ($) {

      $("#a-del").on('click', function () {
        Cookies.set('name', 'zhangsan')
      })

    })

  </script>


</body>
</html>



<!-- 利用cook解决刷新后筛选会变化的问题 -->