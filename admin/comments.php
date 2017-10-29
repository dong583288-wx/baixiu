<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul id="pagination" class="pagination pagination-sm pull-right">
          <!-- <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li> -->
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody id="list">

        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <!-- 引入分页插件 -->
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>

  <!-- 引入jsrender模版引擎 -->
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <!-- 借助Type不等于text/javascript的时候不做js执行
  自定义类型是x-(模版名字)开头
  :代表输出,不用写属性名,前面千万不能加空格-->
  <script id="comments_tmpl" type="text/x-jsrender">

    {{for comments}}
      <tr>
        <td class="text-center"><input type="checkbox"></td>
        <td>{{: author }}</td>
        <td>{{: content }}</td>
        <td>《{{: post_title }}》</td>
        <td>{{: created}}</td>
        <td>{{: status === 'approved' ? '已批准' : status === 'held' ? '待审' : '拒绝' }}</td>
        <td class="text-center">
          <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
          <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
        </td>
      </tr>
      {{/for}}
  </script>



  <!-- ajax获取用户评论 -->
  <script >
    $( function  ($) {

    function loadData (index) {


      //发送ajax请求获取界面
      $.ajax({
      //一般吧ajax请求的地址称为接口(api)
      url: '/admin/api/comments-json.php',
      type: 'get',
      //如果服务端设置了返回格式Content-Type:applica  tion/json,客户端可以不设置dataType:json
      dataType: 'json',
      //响应成功后在拿数据
      //data如果是get请求方式,会自动加到?后面
      data: { page: index },
      success: function (res) {
        //利用模版引擎讲数据渲染到表格中
        //1.引入模版引擎的库文件
        //2.准备一个模版
        //3.准备一个数据
        //4.通过模版引擎提供的某个api,讲模版和数据融合在一起
        //jsrender支持jquery


        //模版数据一般叫上下文
        var context = { comments: res.comments }
        var html = $("#comments_tmpl").render(context)
        //讲html加入tbody中
        $("#list").html(html)


        // 当能够获取到总页数的时候在正确展示分页
        // 由于此分页插件只能调用一次,所以在调用之前先销毁
        $("#pagination").twbsPagination('destroy')
        //

        $("#pagination").twbsPagination({
        //总页数不能写实,必须通过服务端获取
        totalPages: res.total_pages,
        //多少个分页按钮(奇数)
        visiablePages: 5,
        // 默认其实也是当前页
        startPage: index,
        // 每次初始化成第一页后调用loadData函数又会初始化成第一页,所以关闭插件中的默认点击初始化页数1
        initiateStartPageClick:false,
        // 修改默认显示按钮名
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '末页',
        onPageClick: function (e,page) {
          //页码发生改变的事件
          loadData(page)
        }
      })



      }

    } )

  }
      //服务端接口已经做好了分页返回数据接口
      //客户端需要传递分页的参数
      //利用分页插件
      $("#pagination").twbsPagination({
        //总页数不能写实,必须通过服务端获取
        totalPages: 10,
        //多少个分页按钮(奇数)
        visiablePages: 5,
        // 页面点击事件,page点的第几页,e事件参数
        onPageClick: function (e,page) {
          //页码发生改变的事件
          loadData(page)
          console.log(e);
        }
      })

  })



  </script>


</body>
</html>
