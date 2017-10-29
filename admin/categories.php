<?php

//载入全部的公共函数
require_once '../functions.php';

//判断是否登录
xiu_get_current_user();
//处理新增分类的逻辑
function add_category ()
{
  //1.获取客户端提交的数据
  //2.校验
  //3.持久化(保存)
  //4.响应
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    //表单么踹填写完整
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }
  //填写完整后获取
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  //执行添加语句
  $sql = "insert into categories values (null, '{$slug}', '{$name}');";

    //接收改语句影响的行数
    $affected_rows = xiu_execute($sql);
    echo $affected_rows;
    //如果影响语句为1,改成功
    if ($affected_rows === 1) {
      $GLOBALS['success'] = '添加成功';
    }
}

//编辑功能(编辑提交到数据库,必须加上id,判断数据库中是否存在)
function edit_category () {
  if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['slug'])) {
    //列表每天写完整,直接结束
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }

  $id = $_POST['id'];
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  //改数据库中对应id的数据
  $sql = "update categories set slug = '{$slug}', name = '{$name}' where id = {$id}";

  //执行增删改语句,判断受影响的行数
  $affected_rows = xiu_execute($sql);

  if ($affected_rows === 1) {
    $GLOBALS['success'] = '修改成功';
    unset($_POST['name']);
    unset($_POST['slug']);
  }
}

//业务上先处理增删改,最后查询
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(empty($_POST['id'])) {
    add_category();
  } else {
    edit_category();
  }
}

//获取页面中全部分类的数据
$categories = xiu_fetch_all('select * from categories;');

?>


<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message; ?>
        </div>
      <?php endif ?>

      <?php if (isset($success)): ?>
        <div class="alert alert-danger">
          <strong>成功！</strong><?php echo $success; ?>
        </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = "post">
            <h2>添加新分类目录</h2>
            <!-- 隐藏域的特点是看不见,但是可以像其他表单一样提交数据 -->
            <input type="hidden" name="id" id="id" value="0">

            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value = "<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ?>">

              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <!-- 添加一个取消按钮 -->
              <button type="button" class="btn btn-default btn-cancel" style="display: none;">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="btn_delete" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
               <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <!-- 遍历结果集 -->
            <?php foreach ($categories as $item): ?>

              <tr>
              <!-- 给单选框设置自定义属性,吧结果集中的值赋值给这个属性 -->
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>

                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>

                <td class="text-center">

                  <button  class="btn btn-info btn-xs btn-edit" data-id="<?php echo $item['id']; ?>" data-name="<?php echo $item['name'] ?>" data-slug="<?php echo $item['slug'] ?>">编辑</button>

                  <a href="/admin/categories_del.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <script>


    /*//小的复选框
    var $tbodyChangebox = $('tbody input')
    //批量删除
    var $btnDelete = $("#btn_delete")
    //任意复选框发生变化,都执行下面的代码
    $tbodyChangebox.on('change',function () {
      //定义一个标识变量
      var show = false
      $tbodyChangebox.each(function (i,item) {
        //任意一个复选框的属性为true,都显示出批量删除按钮
        if ($(item).prop('checked')) {
          show = true ;
        }
      })
      //当show为true时,显示,反之隐藏
      show ? $btnDelete.fadeIn() : $btnDelete.fadeOut()

    })
*/

    // version 2
    // ==================================
    // 在入口函数传$,相当于在自调用函数传window,为了效率
    /*$(function ($) {
      //获取元素
      var $tbodyCheckboxs = $('tbody input')
      var $btnDelete = $("#btn_delete")

      //定义一个数组存放选中行对应的id
      var checkeds = []
      $tbodyCheckboxs.on('change', function () {
        //只要有任意一个复选框选择状态变化都会执行
        var $this = $(this)
        var id = $this.attr('data-id')

        if ($this.prop('checked')) {
          checkeds.push(id);
        } else {
          //取消选中状态则删除数组中对应的一项
          checkeds.splice(checkeds.indexOf(id), 1)
        }

        //根据有没有选中显示或隐藏
        checkeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
        //改变批量删除链接的问号传参(吧批量删除按钮的href改为指向categories_del,并携带id)
        $btnDelete.attr('href','/admin/categories_del.php?id=' + checkeds)
      })

    })*/



// version 3=============================

$(function ($) {
  var $btnDelete = $("#btn_delete")

  //定义一个数组存放选中行中对应的id
  var checkeds = []

  //事件委托的方式效率高
  $('tbody').on('change', 'input', function () {
    //只要任意一个复选框变化都会选中这里
    var $this= $(this)
    //data-xxx是h5的标准,原生js用dataset
    var id = $this.data('id')

    if ($this.prop('checked')) {
      //解决重复id
      checkeds.indexOf(id)===-1 && checkeds.push(id)

    } else {
      checkeds.splice(checkeds.indexOf(id), 1)
    }
    //根据有没有选中显示或者隐藏
    checkeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
    //改变批量删除链接的问号参数
    $btnDelete.attr('href', 'admin/categories_del.php?id=' + checkeds)
  })

  // 全选 、 全不选
  // 获取tbody所有的input多选框
      var $tbodyCheckboxs = $('tbody input')
      //thead多选按钮改变的时候
      $('thead input').on('change', function () {

        //重复id问题
        //checkeds = [];

        var checked = $(this).prop('checked')
        //把thead选框的选中状态赋值给所有的tbody选框
        $tbodyCheckboxs
          .prop('checked', checked)
          .trigger('change')
      })

      //编辑功能====================
      $('tbody').on('click', '.btn-edit', function () {
        //讲当前行中的数据信息展示到左边的表单中
        //data只能获取到data-id的属性值
        var id = $(this).data('id')
        console.log(id);
        var name = $(this).data('name')
        var slug = $(this).data('slug')

        //改变左侧提交的提示,显示取消按钮
        $('form h2').text('编辑分类')
        $('form .btn-save').text('保存')
        $('form .btn-cancel').fadeIn()
        //设置隐藏域中的value为获取到的数据库中的id
        $('#id').val(id)
        $('#name').val(name)
        $('#slug').val(slug)
      })

      //取消编辑
      $('.btn-cancel').on('click', function () {
        //当点击取消按钮的时候,重新显示添加时的状态
        $('form h2').text('添加新分类目录')
        $('form .btn-save').text('添加')
        $('form .btn-cancel').fadeOut()
        $('#id').val(0)
        $('#name').val('')
        $('#slug').val('')
        //button默认提交,所以return false阻止它提交
        return false

      })

})


  </script>

</body>
</html>
