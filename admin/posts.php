<?php 
require_once '../functions.php';
xiu_get_current_user();

/* =============分类筛选============== */
// 在没有进入判断语句的时候为true,where语句等于没有存在，解决了$_GET['category']='all'时的问题
// 连接赋值运算符（.= ）
$where = '1 = 1';
// 解决分类与分页结合的问题，把两个变量保存起来再与页码一起放到URL地址
$search = '';
if(isset($_GET['category']) && $_GET['category'] !== 'all' ){
   $where .= ' and posts.category_id='.$_GET['category'];
   $search .='&category='.$_GET['category'];
} 
// 状态筛选 
if(isset($_GET['status']) && $_GET['status'] !== 'all'){
  $where .= " and posts.status='".$_GET['status']."'";
  $search .= '&status='.$_GET['status'];
}
// $where => 1=1 and posts.category_id=1 and posts.status='published'


/*=======分页===========*/
// 接收页码参数，进行分页
$page = isset($_GET['page'])? (int)$_GET['page']:1;
$size = 20;

// 排除用户在URL上输入的不可能值（负数页码）
if($page < 1){
  header('Location: /admin/posts.php?page=1'.$search);
}

// 计算总页数，向上取整 （向下取整为 floor（））
$total_count = xiu_fetch_one("SELECT count(1) as num FROM posts
  INNER JOIN categories on posts.category_id = categories.id
  INNER JOIN users on posts.user_id = users.id
  WHERE {$where};")['num'];
  $total_pages = (int)ceil($total_count/$size);//==>51

// 排除用户在URL上输入的不可能值（超总页码）
if($page > $total_pages){
  header('Location: /admin/posts.php?page='.$total_pages.$search);
}

// 计算越过的数据条数
$offset = ($page-1)*$size;
// 计算页码
$visiable = 5;
$region = ($visiable-1)/2;//左右区间
$begin = $page - $region;
$end = $page + $region;

// 最小页码和最大页码的极端值
if ($begin < 1) {
  $begin = 1;
  $end = $begin +$visiable-1;
}
if ($end > $total_pages){
  $end = $total_pages;
  $begin = $end - $visiable + 1;
  // 数据可能不足五页
  if($begin < 1) {
    $begin = 1;
  }
}
 /*分页功能：
  。当前页码显示高亮
  。左侧和右侧各有n个页码（看你页码要显示几条）
  。开始页码不能小于1
  。结束页码不能大于最大页数==>
  。当前页码不为1 时显示上一页
  。当前页码不为最大值时显示下一页
  。当开始页码不等于1时显示省略号
  。当结束页码不等于最大是显示省略号*/


// 数据库多表查询，关联数据查询
$posts = xiu_fetch_all("SELECT 
posts.id,
posts.title,
users.nickname AS user_name,
categories.name AS category_name,
posts.created,
posts.status
FROM posts 
INNER JOIN categories on posts.category_id = categories.id
INNER JOIN users on posts.user_id = users.id
WHERE {$where}
ORDER BY posts.created desc
limit {$offset},{$size}");

// 查询分类
$categories = xiu_fetch_all('SELECT * FROM categories');

/*处理数据格式转换*/
// 处理发布状态
function xiu_convert_status($status){
  // ==========version 2 (array 关联数组)===============
  $convert_status= array( 
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );  
  return isset($convert_status[$status])? $convert_status[$status]:'未知';
}
//=======version 1 (switch case 方法)===================
  // switch ($status) {
  //   case 'published': 
  //   $convertStatus="已发布";
  //     break;
  //   case 'drafted': 
  //   $convertStatus='草稿';
  //     break;
  // }
  // return isset($convertStatus)? $convertStatus :'未知'; 
/*处理发布时间*/
function xiu_convert_created($created){
  // 将时间格式化成时间戳
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r> h:i:s', $timestamp);
}
/*这调用函数的方法对于多条数据查询不好，要多次查询数据库，降低性能*/
// function get_user ($user_id){
//   return xiu_fetch_one("SELECT * FROM users WHERE id={$user_id}")['nickname'];
// }
// function get_category ($category_id){
//   return xiu_fetch_one("SELECT * FROM categories WHERE id={$category_id}")['name'];
// }
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
    <?php include 'inc/navbar.php'; ?>
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
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($categories as $value): ?>
              <option value="<?php echo $value['id']; ?>"<?php echo isset($_GET['category']) &&$_GET['category']==$value['id']?' selected':''; ?>><?php echo $value['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) &&$_GET['status']=='drafted'?' selected':''; ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) &&$_GET['status']=='published'?' selected':''; ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) &&$_GET['status']=='trashed'?' selected':''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">      
          <li<?php echo $page==1?' style="display: none"':''; ?>><a href="?page=<?php echo $page - 1 .$search; ?>"><<</a></li>
          <li<?php echo $begin==1?' style="display: none"':''; ?>><a href="#">...</a></li>
          <?php for ($i=$begin; $i <= $end; $i++):  ?>
             
            <li<?php echo $i === $page?' class="active"':''; ?>><a href="?page=<?php echo $i.$search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>    
          <li<?php echo $end==$total_pages?' style="display: none"':''; ?>><a href="#">...</a></li>   
          <li<?php echo $page==$total_pages?' style="display: none"':''; ?>><a href="?page=<?php echo $page + 1 .$search; ?>">>></a></li>        
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
            <td><?php echo $value['title']; ?></td>
            <!-- <td><?php //echo get_user($value['user_id']); ?></td>
            <td><?php //echo get_category($value['category_id']); ?></td> -->
            <td><?php echo $value['user_name']; ?></td>
            <td><?php echo $value['category_name']; ?></td>
            <td class="text-center"><?php echo xiu_convert_created($value['created']); ?></td>
            <!--  $value['status'] === 'published'? '已发布':'草稿' 三元表达式不是上策，当选择多了怎么办 -->
            <td class="text-center"><?php echo xiu_convert_status($value['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
