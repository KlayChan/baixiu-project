<?php 
require_once '../functions.php';
xiu_get_current_user();
 ?>
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
  <!-- 传统页面引用进度条 -->
  <script>NProgress.start()</script>

  <div class="main">
   <?php include 'inc/navbar.php'; ?>
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
        <ul class="pagination pagination-sm pull-right">
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
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
       <tbody>
          <!-- <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> --> 
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <!-- 自定义jsrender模板 -->
  <script type="text/x-jsrender" id="j-specCard">
    {{for comments}}
          <tr {{if status == 'held'}}class="warning" {{else status == 'rejected'}}class="danger" {{/if}} data-id={{:id}}>
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td>《{{:post_title}}》</td>
            <td>{{:created}}</td>
            <td>{{:status}}</td>
            <td class="text-center">
              {{if status == 'held'}}
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
              {{/if}}
              <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
            </td>
          </tr>
    {{/for}}      
  </script>
  <script>
    // ajax 中引用nprogress 进度条
    $(document)
     .ajaxStart(function () {
       NProgress.start()
     })
     .ajaxStop(function () {
       NProgress.done()
     })
    var currentPage = 1;
    function loadPageData (page){
      // 发送ajax请求，接收数据
      $.get('/admin/api/comments.php', { page: page}, function(res){
        $('.pagination').twbsPagination({
          totalPages: res.total_pages,
          visiblePages: 5,
          initiateStartPageClick: false,
          first: '首页',
          prev: '前一页',
          next: '下一页',
          last: '尾页',
          onPageClick: function (event, page) {
           loadPageData(page);
          }
        });       
        // 将获取的数据渲染页面
        jsRenderTpl = $.templates('#j-specCard'),
        //模板与数据结合,以创建对象的方式传入参数
        finalTpl = jsRenderTpl({comments: res.comments});
        // html(),jq里面的方法
        $('tbody').html(finalTpl);
        currentPage = page;
      });
    }
    loadPageData (currentPage);

    //========删除功能==========
    // 由于是异步请求，注册事件会过早，所以要用事件委托
    $('tbody').on('click', '.btn-delete', function(){
      var id = $(this).parent().parent().data('id');
       $.get('/admin/api/comments-delete.php', { id: id}, function(res){
        if(!res) return
         loadPageData (currentPage);
       })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
