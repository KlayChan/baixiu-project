<?php 
require_once '../config.php';
function login(){
  //表单校验三部曲
  // 1.接收并校验
  // 2.持久化
  // 3.响应
  if (empty($_POST['email'])){
    $GLOBALS['message'] = '请输入邮箱地址';
    return;
  }
  if (empty($_POST['password'])){
    $GLOBALS['message'] = '请输入密码';
    return;
  }
  // 获取变量值
  $email = $_POST['email'];
  $password = $_POST['password'];
  // 连接数据库
  $con = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
  // $con = mysqli_connect('localhost', 'root', '123456', 'baixiu-dev');
  if(!$con){
    exit('连接数据库失败');
  }
  $query = mysqli_query($con, "SELECT * FROM users WHERE email = '$email' limit 1;");
  if(!$query){
    $GLOBALS['message'] = '登录失败，请重试';
    return;
  }
  $user = mysqli_fetch_assoc($query);
  if(!$user){
    $GLOBALS['message'] = '密码与邮箱不匹配';
    return;
  }
  // 现在的MD5已经不安全了
  if($user['password'] !== MD5($password)){
    $GLOBALS['message'] = '密码与邮箱不匹配';
    return;
  }
  // 开启回话
  session_start();
  // 记录登录状态
  $_SESSION['current_login_user'] = $user;
  // 响应
  header('Location: /admin/index.php');

}
// 判断提交的方式
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  login();
}
// 清除session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) &&$_GET['action'] === 'logout'){
  // 不开启会话会报错
  session_start();
  unset($_SESSION['current_login_user']);
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- novalidate html5 新增表单属性 取消浏览器的校验功能，不友好，
         autocomplete="off" 关闭自动补全功能 -->
    <form class="login-wrap<?php echo isset($message)?' shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <!-- 这里的语法是后面加 ： 不是 ; -->
      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <!-- 让value 保持状态 -->
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus <?php echo isset($_POST['email'])? 'value="'.$_POST['email'].'"':'';?>>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    // 入口函数 很重要 跟以前不一样的是加了个参数$
    // 1.单独作用域
    // 2.确保页面加载过后执行
    $(function($){
      $('#email').blur(function(){
        var emailcheck = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;
        var value = $(this).val();
        // 验证表单域是否为空，邮箱校验       
        if (!value || !emailcheck.test(value)) return
        // if($(this).val()==='') return;
        // if(!emailcheck.test($(this).val())) return;
        // 以字面量的方式创建对象
        $.get('/admin/api/avatar.php', { email: value}, function(avatar){
           $('.avatar').fadeOut().on('load', function(){
            // 图片的onload函数，等到图片完全加载成功过后才执行（回调函数）
             $(this).fadeIn();
           }).attr('src', avatar); 
        })
      })
    })

   
    // $(function($){
    //   $('#email').blur(function(){
    //     console.log($(this).val());
    //   })
    // })
  </script>
</body>
</html>
