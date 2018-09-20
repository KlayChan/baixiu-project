<?php 
require_once '../../functions.php';

// 接收并判断参数
$page = empty($_GET['page'])? 1 : intval($_GET['page']);
// 定义跨越条数和每页条数
$length = 50;
$offset = ($page-1) * $length;
$comments = xiu_fetch_all("SELECT 
comments.*,
posts.title as post_title
FROM comments
INNER JOIN posts ON comments.post_id=posts.id
LIMIT {$offset},{$length};");

// 计算分页的总页数
$total_comments = xiu_fetch_one('SELECT count(1) as count FROM comments
INNER JOIN posts on comments.post_id=posts.id')['count'];
$total_pages = ceil($total_comments/$length);

// 将数据转换成字符串（序列化）
$res = json_encode(array(
   'total_pages'=> $total_pages,
   'comments'=> $comments
   ));
header('Content-Type: application/json');
echo $res;
