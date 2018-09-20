<?php 
require_once '../functions.php';
xiu_get_current_user();


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
        <h1>豆瓣热映</h1>
      </div>
      <div class="page-action">
        <ul class="pagination pagination-sm pull-right">      
          <li><a href="#">1</a></li>
          <li class="active"><a href="#">2</a></li>
          <li><a href="#">3</a></li>        
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>影片热映排行</th>
            <th>导演</th>
            <th>主演</th>
            <th class="text-center">类型</th>
            <th class="text-center">豆瓣评分</th>
          </tr>
        </thead>
        <div>
          <ul id="movie"></ul>
        </div>
        <tbody>
           
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page = 'douban'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $.ajax({
      url: 'http://api.douban.com/v2/movie/in_theaters',
      dataType: 'jsonp',
      success: function (res) {
        $(res.subjects).each(function(i,item){
          $('tbody').append(`<tr><td class="text-center"><input type="checkbox"></td><td>${item.title}</td><td>${item.directors[0].name}</td><td>${item.casts[0].name}</td><td>${item.genres}</td><td>${item.rating.average}</td></tr>`)
        })
        // 计算总页数 
        $size = 10;
        $total_count = res.total;
        $total_pages = int(ceil($total_count/$size));
        
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
      }
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
