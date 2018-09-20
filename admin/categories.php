<?php 
require_once '../functions.php';
xiu_get_current_user();
function add_categories(){
  $name = $_POST['name'];
  $slug = $_POST['slug'];
  if(empty($name) || empty($slug)){
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请完整输入添加信息';
    return;
  }
  $rows = xiu_execute("INSERT INTO categories VALUES (NULL, '{$slug}', '{$name}');");
  // $GLOBALS['success'] =$rows >0;
  // $GLOBALS['message'] = $rows <= 0? '添加失败':'添加成功';
  if($rows > 0){
    $GLOBALS['message'] = '添加成功';
    $GLOBALS['success'] = true;
  }else {
    $GLOBALS['message'] = '添加失败';
    $GLOBALS['success'] = false;
  }
}
function edit_categories() {
  global  $edit_current_category;
  $name = empty($_POST['name'])?  $edit_current_category['name']:$_POST['name'];
  // 接收更改信息呈现到页面（同步数据）
  $edit_current_category['name']=$name;
  $slug = empty($_POST['slug'])?  $edit_current_category['slug']:$_POST['slug'];
  $edit_current_category['slug']=$slug;
  $id = $_GET['id'];
  // 这个没有三元表达式的好像也可以 ？？不行，当你清空表单时就起作用了
  // $name = $_POST['name'];
  // $edit_current_category['name']=$name;
  // $slug = $_POST['slug'];
  // $edit_current_category['slug']=$slug;
  $rows = xiu_execute("UPDATE  categories  SET slug='{$slug}',name='{$name}' WHERE id={$id}");
  // $GLOBALS['success'] =$rows >0;
  // $GLOBALS['message'] = $rows <= 0? '添加失败':'添加成功';
  if($rows > 0){
    $GLOBALS['message'] = '更新成功';
    $GLOBALS['success'] = true;
  }else {
    $GLOBALS['message'] = '更新失败';
    $GLOBALS['success'] = false;
  }

}
// 经过两次id识别，在表单提交的时候也要设置id
if (empty($_GET['id'])){
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
   add_categories(); 
  }
  // echo '11111';
}else {
  $edit_current_category =xiu_fetch_one('SELECT * FROM categories WHERE id='.$_GET['id']);
  if($_SERVER['REQUEST_METHOD'] === 'POST'){    
   edit_categories();   
  }
}

$categories = xiu_fetch_all('SELECT * FROM categories;');


 ?>
<!DOCTYPE html>
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
    <?php include 'inc/navbar.php'; ?>
    </nav>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <?php if(isset($message)): ?>
        <?php if($success): ?>
          <div class="alert alert-success">
            <strong>成功！</strong><?php echo $message; ?>
          </div>
        <?php else: ?>
        <!-- 有错误信息时展示 -->
          <div class="alert alert-danger">
            <strong>错误！</strong><?php echo $message; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="row">
        <?php if(isset($_GET['id'])): ?>
        <div class="col-md-4">
          <!-- 两次页面请求都要id识别，这个是为了在点击更新按钮识别页面 -->
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $edit_current_category['id']; ?>" method="post">
            <h2>编辑《<?php echo  $edit_current_category['name'];?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" value="<?php echo  $edit_current_category['name'];?>" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php echo  $edit_current_category['slug'];?>" autocomplete="off">
              <p class="help-block">https://klaychan/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">更新</button>
            </div>
          </form>
        </div>
        <?php else: ?>
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" autocomplete="off">
              <p class="help-block">https://klaychan/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <?php endif; ?>   
        
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
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
              <?php foreach ($categories as $value): ?>
                  <tr>
                    <td class="text-center"><input type="checkbox" data-id="<?php echo $value['id']; ?>"></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['slug'] ?></td>
                    <td class="text-center">
                      <a href="/admin/categories.php?id=<?php echo $value['id']?>" class="btn btn-info btn-xs">编辑</a>
                      <a href="/admin/category-delete.php?id=<?php echo $value['id']?>" class="btn btn-danger btn-xs">删除</a>
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
  <?php include 'inc/sidebar.php'; ?>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function($){
      var $tbodyCheckboxs = $('tbody input');
      var $theadCheckboxs = $('thead input');
      var $btnDelete = $('#btn_delete'); 
      var allChecked = [];
      $tbodyCheckboxs.on('change', function(){
        // 获取id的三种方法
        // console.log($(this).attr('data-id'))**获取的是字符串数字;
        // console.log($(this).data('id'))**获取的是数字;
        // console.log(this.dataset['id'])**获取的是字符串数字;
        var id = $(this).data('id');
        if($(this).prop('checked')){
          // 短路运算符 ，判断是否存在重复的id
           // allChecked.indexOf(id) === -1 || 
           allChecked.includes(id) || allChecked.push(id);
         }else {
          allChecked.splice(allChecked.indexOf(id), 1);
         }
          allChecked.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
          $btnDelete.attr('href', '/admin/category-delete.php?id='+allChecked);
         // $btnDelete.prop('search','?id='+allChecked);
      })
      // 全选和全不选  
      $theadCheckboxs.on('change', function(){
        var checked = $(this).prop('checked');
        $tbodyCheckboxs.prop('checked', checked).change();
        // if( $(this).prop('checked')){
        //   $tbodyCheckboxs.prop('checked', true).change();
        //   $btnDelete.fadeIn();
        //  }else {
        //   $tbodyCheckboxs.prop('checked',false);
        //   $btnDelete.fadeOut();
        //  }
       
      })
    })
      // ============version1=================
      // 每次都要遍历所有的复选框，效率低  
      // $tbodyCheckboxs.on('click', function(){
      //   // 这个变量要放在这个函数里面，有任意一个被选中就显示，否则就隐藏
      //   var flag = false;
      //   $tbodyCheckboxs.each(function(i, item){
      //     if($(item).prop('checked')){
      //        flag = true;
      //     }
      //   })
      //   flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
      //   // if($('tbody input').prop('checked')){
      //   //   console.log('111');
      //   // }
      // })   
    
  </script>
</body>
</html>
