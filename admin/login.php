<?php
//载入文件
require '../config.php';



//接收表单数据
function post()
{
  if (empty($_POST['email'])) {

  $GLOBALS['error_msg'] = "用户名没有填写";

  return;
}

if (empty($_POST['password'])) {

  $GLOBALS['error_msg'] = "密码没有填写";

  return;
}

$email = $_POST['email'];

$password = $_POST['password'];


//在数据库中校验用户名和密码

//建立数据库链接
$conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);

if (!$conn) {
  die('链接数据库失败');
  return;
}

//根据邮箱查询数据库用户信息,limit是为了提高效率

$result = mysqli_query($conn, "select * from users where email = '$email' limit 1;");

if (!$result) {
  $GLOBALS['error_msg'] = '用户名错误';
  return;
}

 // 如果数据没有发生任何变化 受影响行数也是零
  if (mysqli_affected_rows($conn) !== 1) {
    $GLOBALS['error_str'] = '更新失败';
    return;
  }


//用变量接收查询的对象集
$user = mysqli_fetch_assoc($result);

//用户名正确后
//$GLOBALS['src'] = $user['avatar'];


//判断密码
if ($user['password'] !== $password) {
  $GLOBALS['error_msg'] = '密码错误';
  return;
}



/*if ($email !== 'admin@foo.com') {

  $GLOBALS['error_msg'] = '用户名或密码错误！';

  return;
}

if ($password !== 'admin') {

  $GLOBALS['error_msg'] = '用户名或密码错误！';

  return;
}*/



//利用cookie保持用户名
//setcookie('is_logged_in', 'true');

//利用session
//启用新会话就新建一个箱子,使用已有会话就打开预留的箱子
session_start();

$_SESSION['is_logged_in'] = $user;

//响应,跳转
header ('location:/admin/');

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  post();

}

?>




<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>


  <script src="/static/assets/vendors/jquery/jquery.min.js"></script>

  <script>

  $(function () {


    $("#email").on("blur",function () {

      $.get('ajax.php', { email: this.value }, function (res) {

        $("#avatar").attr("src",res)


      })

    })


  })

  </script>


  <div class="login">
    <form class="login-wrap" method = 'post' action="<?php echo $_SERVER['PHP_SELF'] ?>">
      <img class="avatar"  id="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error_msg ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" class="form-control" placeholder="邮箱" autofocus  name = 'email' value = '<?php echo  isset($_POST['email']) ? $_POST['email'] : ''; ?>'>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" class="form-control" placeholder="密码" name = 'password'>
      </div>
      <button type="submit" class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
</html>
