<?php 
require_once '../../functions.php';
// 现象sql注入，如输入的id=1 or 1=1,结果永远是true，等价于没有子条件
// 解决方法有很多 取整（int)===>单条删除 ，下面多条删除也要解决这个问题
$id =$_GET['id'];
if(empty($id)){
	exit('请传入必要参数');
}
$res = xiu_execute('DELETE FROM comments WHERE id in ('.$id.');');

// header('Location: /admin/categories.php');
header('Content-Type: apllication/json');
echo json_encode($res > 0);