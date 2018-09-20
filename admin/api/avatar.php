<?php 
require_once '../../config.php';
// 获取ajax提交过来的变量
$email = $_GET['email'];
if (empty($email)){
  exit('请传入必要参数');
}
$con = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
  // $con = mysqli_connect('localhost', 'root', '123456', 'baixiu-dev');
if(!$con){
    exit('连接数据库失败');
}
$query = mysqli_query($con, "SELECT avatar FROM users WHERE email='$email';");
if(!$query){
    exit('连接数据库失败2');
}
$res = mysqli_fetch_assoc($query);
echo $res['avatar'];
  // $user = mysqli_fetch_assoc($query);
  // if(!$user){
  //   exit('连接数据库失败3');
  // }
// while ($user = mysqli_fetch_assoc($query)) {
//   foreach ($user as $avatar) {
//   		var_dump($avatar);
//   }
// }
  // $avatar = $user['avatar'];
  // echo $avatar;
