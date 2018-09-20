<nav class="navbar">
      <button class="btn btn-default navbar-btn fa fa-bars"></button>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/admin/profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <!-- 退出的重点是清除session，有两个方法：一个传参，然后在登录页面判断参数，清除
             二是跳转到另一个页面执行清除功能，然后跳转回登录页面 -->
        <li><a href="/admin/login.php?action=logout"><i class="fa fa-sign-out"></i>退出</a></li>
      </ul>
</nav>