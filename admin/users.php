<?php

//载入群补的公共函数
require_once '../functions.php';

//判断是否登录
xiu_get_current_user();


//处理新增分类
function add_user () {
  //获取客户端提交的数据
  //校验
  //持久化
  //响应

  if (empty($_POST['slug']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['nickname']) ) {
    $GLOBALS['message'] = '请完整填写用户信息';
    return false;
  }
  //接收用户信息
  $slug = $_POST['slug'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $nickname = $_POST['nickname'];


  //校验头像
  if (!(isset($_FILES['avatar']) && $_FILES['avatar']['error'] ===UPLOAD_ERR_OK)) {
    //头像上传失败
    $is_new = false;
  } else {
    //获取文件扩展名
    $ext_name = pathinfo($_FILES['avatar']['name'])['extension'];
    //目标目录  uniqid()防止文件重名
    $target = '../static/uploads/img-'. uniqid() . '.' . $ext_name;
    //把文件从零食目录移动到目标目录
    $is_ok = move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
    //判断上传失败
    if (!$is_ok) {
      $GLOBALS['message'] = '头像上传失败';
    }

    $is_new = true;
  }
  //上传成功就获取新图路径,上传失败就用默认的
  $avatar = $is_ok ? substr($target, 2) : '../static/assets/img/default.png';

  //sql添加语句
  $sql = "insert into users values (null,'$slug','$email','$password','$nickname', '$avatar',null,'activated');";

  //判断影响的行数是否为1
  $affected_rows = xiu_execute($sql);
// var_dump($affected_rows);
  if ($affected_rows === 1) {
    $GLOBALS[message] = '添加成功';
  }
}

//执行编辑语句
function edit_user () {
  if (empty($_POST['id']) || empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password']) ) {

    //用户信息不完整
    $GLOBALS['message'] = '请完整填写用户信息';
    return;
  }
  //接收用户信息
  $id = $_POST['id'];
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $password = $_POST['password'];
  $nickname = $_POST['nickname'];

  //修改sql中的数据
  $sql = "update users set email = '{$email}' slug = '{$slug}' password = '{$password}' nickname = '{$nickname}' where id = {$id}";

  //受影响的行数
  $affected_rows = xiu_execute($sql);

  if ($affected_rows === 1) {
    $GLOBALS['success'] = '添加用户信息成功';
    unset($_POST['email']);
    unset($_POST['slug']);
    unset($_POST['password']);
    unset($_POST['nickname']);
  }
}

//先处理增删改,最后查询
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //判断携带了id就是修改
  if ( empty($_POST['id']) ) {
    add_user();
  } else {
    edit_user;
  }
}

//获取数据库中所有的用户信息
$user_all = xiu_fetch_all('select * from users;');

?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>提示！</strong><?php echo $message ?>
        </div>
      <?php endif ?>

      <?php if (isset($success)): ?>
        <div class="alert alert-danger">
          <strong>成功！</strong><?php echo $success ?>
        </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
        <!-- 注意文件上传要加multipart="form-data" -->
          <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
            <h2>添加新用户</h2>

            <!-- 隐藏域的特点是看不见，但是也可以像其他表单域一样提交数据 -->
            <input type="hidden" id="id" name="id" value="0">

            <!-- 头像 -->
            <div class="form-group">
              <label for="avatar">头像</label>
              <input id="avatar" class="form-control" name="avatar" type="file">

              <img id="preview" class="help-block thumbnail" style="display: none">
              <i class="mask fa fa-upload"></i>

            </div>

            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>

              <button class="btn btn-default btn-cancel" type="button" style="display: none;">取消</button>

            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_del" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center"  height="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach ($user_all as $value): ?>

                <tr>

                  <td class="text-center"><input type="checkbox" data-id="<?php echo $value['id'] ?>"></td>

                  <td class="text-center"><img class="avatar" src="<?php echo isset($value['avatar']) ? $value['avatar'] : "" ?>"></td>

                  <td><?php echo $value['email'] ?></td>
                  <td><?php echo $value['slug'] ?></td>
                  <td><?php echo $value['nickname'] ?></td>
                  <td><?php echo $value['status'] ?></td>

                  <td class="text-center">
                  <a href="javascript:;" class="btn btn-default btn-xs btn-edit" data-email="<?php echo $value['email'] ?>" data-slug="<?php echo $value['slug'] ?>" data-password="<?php echo $value['password'] ?>" data-nickname="<?php echo $value['nickname'] ?>">编辑</a>
                  <a href="/admin/user_del.php?id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>

              <?php endforeach ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>


  <script>
    $(function ($) {
      //批量删除按钮
      var $btn_del = $("#btn_del")
      //定义一个数组存放选中行对应的id
      var checkeds = []
      //利用事件委托
      $('tbody').on('change', 'input', function () {
        // 只要有任意一个复选框选择状态变化都执行这里
        var $this = $(this)

        // data获取自定义属性
        var id = $this.data('id')
        //把tbody的选中状态赋值给thead
        if ($this.prop('checked')) {
          checkeds.push(id)
        } else {
          checkeds.splice(checkeds.indexOf(id), 1)
        }
        //根据有没有选中显示或隐藏
        checkeds.length ? $btn_del.fadeIn() : $btn_del.fadeOut()

        //改变批量删除链接和传参
        $btn_del.attr('href', '/admin/user_del.php?id=' + checkeds)
      })

      //全选和全反选
      var $tbodyCheck = $('tbody input')
      $('thead input').on('change', function () {
        var checked = $(this).prop('checked')

        $tbodyCheck.prop('checked', checked).trigger('change')
      })


      //编辑功能
      $('tbody').on('click', '.btn-edit', function () {
          //将列表中的数据展示到左边
          var id = $(this).data('id')
          var email = $(this).data('email')
          var slug = $(this).data('slug')
          var password = $(this).data('password')
          var nickname = $(this).data('nickname')

          //修改提示信息
          $('form h2').text('编辑用户信息')
          $('form .btn-save').text('保存')
          $('form .btn-cancel').fadeIn()
          //设置隐藏域中的id
          $('#id').val(id)
          $('#email').val(email)
          $('#slug').val(slug)
          $('#nickname').val(nickname)
        })

      //取消编辑
      $('.btn-cancel').on('click', function () {
        $('form h2').text('添加新用户')
        $('form .btn-save').text('添加')
        $('form .btn-cancel').fadeOut()
        $('#id').val(0)
        $('#id').val(id)
        $('#email').val('')
        $('#slug').val('')
        $('#nickname').val('')
        $('#password').val('')
        //取消默认提交功能
        return false
      })

      //添加图片预览功能封装
     $("#avatar").on('change', function () {
      //这里的代码会在用户选择文件后出发执行
console.log(this.files);
      //所选文件的长度为0就return掉
      if (!this.files.length) return

      //有可能是多文件上传,所以要选择第0个
      var file = this.files[0]

      //判断图片文件的类型是不是以images/开头的
      if (!file.type.startsWith('image/'))  return

      //为这个图片文件分配一个零食地址
      var url = URL.createObjectURL(file)

      $("#preview").attr('src', url).fadeIn().on('load', function () {

        //吊销这个地址,一定得在onload事件中执行
        URL.revokeObjectURL(URL)
      })

     })

    })

  </script>


</body>
</html>
