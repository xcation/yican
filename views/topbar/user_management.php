    <?php 
      if ($login) { ?>
        <div class="management">
          <a class="nav-username" data-toggle="dropdown" id="nav-username">
            <?=$userName?>
            <i class="icon-dropdown"></i>
          </a>
          
          <ul class="dropdown-menu manager-down" id="manager-dropdown">
            <li>
              <a rel="nofollow" href="/manager">
                <i class="icon-profile"></i>
                个人中心
              </a>
            </li>
            <li>
              <a rel="nofollow" href="/manager/history">
                <i class="icon-history"></i>
                历史订单
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a rel="nofollow" href="/logout">
                <i class="icon-logout"></i>
                登出
              </a>
            </li>
          </ul>
          <script type="text/javascript">
            $(".nav-username").dropdown();
            $(".management").mouseleave(function(){
              $(".management").removeClass('open');
            });
          </script>
        </div>
      <?php }
      else { ?>
        <div> 
          <a href="/login">登录</a>
          <span>/</span>
          <a href="/register">注册</a>
        </div>
      <?php}?>