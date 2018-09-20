<?php
require_once 'config.php'; 
/**
* 封装大家公用的函数
*/
// 开启回话
session_start();
/*封装获取session标识函数*/
// 注意定义函数时，函数名不要与PHP中的内置函数名（一千多个）冲突
function xiu_get_current_user(){
	// 判断是否存在登录标识，没有就跳转到登录界面
	if(empty($_SESSION['current_login_user'])){
	  header('Location: /admin/login.php');
	  exit();
	}
	return $_SESSION['current_login_user'];
}
/*封装数据库查询操作*/
// 查询多条数据
function xiu_fetch_all($sql){
	$con = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
	if(!$con){
		exit('连接失败');
	}
	$query = mysqli_query($con, $sql);
	if(!$query){
		return false;
	}
	while ($row = mysqli_fetch_assoc($query)) {
		$result[] = $row; 
	}
	return $result;

} 

/* 查询单条数据*/
function xiu_fetch_one($sql){
	$res = xiu_fetch_all($sql)[0];
	return isset($res)? $res:null;
}

function xiu_execute($sql){
	$con = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
	if(!$con){
		exit('连接失败');
	}
	$query = mysqli_query($con, $sql);
	if(!$query){
		return false;
	}
	$affected_row = mysqli_affected_rows($con);
	if ($affected_row <= 0) {
		return false;
	}
	return $affected_row;
}