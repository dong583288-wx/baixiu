<?php
//引入函数
require_once '../functions.php';

//判断是否登录
xiu_get_current_user();

//保存文章
/*function add_user () {
  //获取客户端提交的数据
  //校验
  //持久化
  //响应
  if ( empty($_POST['slug']) || empty($_POST[]) )


}*/

?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>

  <!-- 引入富文本css -->
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/simplemde/simplemde.min.css">

</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include './inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">正文</label>
            <!-- simplemde文本框需要用 -->
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->

            <!-- 百度文本框 -->
            <script name="content" type="text/plain" id="content"></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img id="preview" class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <option value="1">未分类</option>
              <option value="2">潮生活</option>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <!-- 引入富文本插件 -->
  <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>

  <!-- 引入百度UEditor -->
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>

  <!-- 引入时间格式化momentjs库 -->
  <script src="/static/assets/vendors/moment/moment.js" type="text/javascript"></script>

  <script>
    $(function  ($) {

      //给文本框加富文本格式
        // new SimpleMDE ({
        //   element: $('#content')[0],
        //   //取消文本检查背景
        //   specified: false
        // })


        //给文本域添加百度的ueditor
        //插上纳入文本域的name
        var ue = UE.getEditor('content')
        //设置编辑器
        //通过查看官网api设置
        ue.ready ( function () {
          ue.setContent('hello word')
          ue.setHeight(600)
        } )


      //添加图片预览
      $("#feature").on('change', function () {

        //这里的代码会在用户选择文件后执行
        if (!this.files.length) return

        //选择了一个文件
        var file = this.files[0]

        //判断文件是否是图片
        //判断文件是否是以images/开头
        if (!file.type.startsWith('image/'))
          return

        //为这个文件分配一个零食地址
        var url = URL.createObjectURL(file)

        $('#preview').attr('src', url).fadeIn().on('load', function () {
          //吊销这个地址,必须在图片的onload事件中执行
          URL.revokeObjectURL(url)
        })
      })


      //js设置时间
      // var date = new Date()
      // console.log(date.getYear() + 1900)
      //
      //原生过于麻烦一般用momentjs库
      var time = moment().format('YYYY-MM-DDTHH:mm')
      console.log(time)
      $("#created").val(time)




    })

  </script>

</body>
</html>
