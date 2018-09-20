<?php
require_once '../functions.php';
xiu_get_current_user();
// AS num 对查询的结果列去别名num（原名是count（1））
// 查询数据库对应数据
$posts = xiu_fetch_one('SELECT count(1) AS num FROM posts;');
$posts_dradted = xiu_fetch_one("SELECT count(1) AS num FROM posts WHERE status = 'drafted';");
$comments = xiu_fetch_one('SELECT count(1) AS num FROM comments;');
$comments_held = xiu_fetch_one("SELECT count(1) AS num FROM comments WHERE status = 'held';");
$categories = xiu_fetch_one('SELECT count(1) AS num FROM categories;');
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="/admin/post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts['num'] ?></strong>篇文章（<strong><?php echo $posts_dradted['num'] ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories['num'] ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments['num'] ?></strong>条评论（<strong><?php echo $comments_held['num'] ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="chart-area"></canvas>
        </div>
        <div id="echarts" class="col-md-4" style="width: 400px;height:400px;"></div>
      </div>
    </div>
  </div>
  <?php $current_page = 'index'; ?>
<!-- 为什么我用绝对路径不行？？ -->
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.bundle.js"></script>
  <script src="/static/assets/vendors/chart/utils.js"></script>
  <script src="/static/assets/vendors/chart/echarts.simple.min.js"></script>
  <script>NProgress.done()</script>
   <script>
     var randomScalingFactor = function() {
      return Math.round(Math.random() * 100);
     };

    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?php echo $posts['num'] ?>,
            <?php echo $categories['num'] ?>,
            <?php echo $comments['num'] ?>
          ],
          backgroundColor: [
            window.chartColors.red,
            window.chartColors.orange,
            window.chartColors.yellow,
          ],
          label: 'Dataset 1'
        }],
        labels: [
          '文章',
          '分类',
          '评论',        
        ]
      },
      options: {
        responsive: true
      }
    };

    window.onload = function() {
      var ctx = document.getElementById('chart-area').getContext('2d');
      window.myPie = new Chart(ctx, config);
    };

    var colorNames = Object.keys(window.chartColors);
    document.getElementById('addDataset').addEventListener('click', function() {
      var newDataset = {
        backgroundColor: [],
        data: [],
        label: 'New dataset ' + config.data.datasets.length,
      };

      for (var index = 0; index < config.data.labels.length; ++index) {
        newDataset.data.push(randomScalingFactor());

        var colorName = colorNames[index % colorNames.length];
        var newColor = window.chartColors[colorName];
        newDataset.backgroundColor.push(newColor);
      }

      config.data.datasets.push(newDataset);
      window.myPie.update();
    });
   </script>
   <script type="text/javascript">
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('echarts'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '站点内容统计：'
            },
            tooltip: {},
            legend: {
                data:['销量']
            },
            xAxis: {
                data: ['文章', '分类', '评论']
            },
            yAxis: {},
            series: [{
                name: '销量',
                type: 'bar',
                data: [<?php echo $posts['num'] ?>,
                      <?php echo $categories['num'] ?>,
                      <?php echo $comments['num'] ?>
                      ]
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
</body>
</html>
