
<div class="content">
    <div id="topbar">
        <div class="head_container container">
            <div id='d-logo'>
              <a id="logo" href="/" title="一餐易餐" alt="一餐易餐" role="logo">
              </a>
            </div>
            <div class="location">
                <div class="switchLoc">
                  <?php
                    if (@$shortName) { ?>
                      <a href="/university/<?=$shortName?>"><?=$university?></a>&nbsp;
                      <?php
                      if (@$storeName) {
                          echo "><a href='/store/{$university_id}/{$store_id}'> $storeName</a>";
                      } ?>
                      <a href="/1">[换地址]</a>
                  <?php 
                  }
                  ?>
                </div>
            </div>
            <div id="topbar_search" class="topbar-search" role="search">
                <?php
                if (@$search_not_avai) { ?>
                <?php } 
                else { ?>
                <form id="tsearch_form" class="tsearch-form" action="/search" method="post">
                    <!-- <input id="tsearch_input" class="tsearch-input" type="text" name="kw" autocomplete="off" placeholder="餐厅，美食…"> -->
                    <input type='search' autocomplete="off" placeholder="搜餐厅、食物" id='search_msg' name='keyword'/>
                    <input type='button' style='padding-top: 1;'class='search-btn' value='搜索'/>
                </form>
                <?php
                } ?>
                <!-- <div id="tsearch_autocomplete" class="tsearch-autocomplete" style="display: none;"></div> -->
                <script type="text/javascript">
                  $(".search-btn").click(function() {
                    if ($('#search_msg').val() == "") {
                      return;
                    }
                    var form = $("#tsearch_form");
                    form.submit();
                  });
                </script>
                <?php
                if (@$shanghu) { ?>
                  <span class='black_a'><a href='/shanghu/logout'>退出</a></span>
                <?php
                }
                else {
                  if (@$login) { ?>
                    <div class="management dropdown">
                        <a class="nav-username dropdown-toggle" data-toggle="dropdown" id="nav-username">
                          <?=$userName?>
                          <i class="icon-dropdown"></i>
                        </a>
                      
                        <ul class="dropdown-menu manager-down" id="manager-dropdown">
                            <li>
                              <a rel="nofollow" href="/management/order">
                                <i class="icon-history"></i>
                                历史订单
                              </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                              <a rel="nofollow" href="/login/logout">
                                <i class="icon-logout"></i>
                                退出
                              </a>
                            </li>
                          </ul>
                          <script type="text/javascript">
                            // $(".nav-username").dropdown();
                            // $(".management").mouseleave(function(){
                            //   $(".management").removeClass('open');
                            // });
                          </script>
                        </div>
                      <?php }
                      else { ?>
                        <div class="management"> 
                          <a href="/login">登录</a>
                          <span>/</span>
                          <a href="/login/register">注册</a>
                        </div>
                    <?php } ?>
                    <div id="topbar_cart" class="dropdown">
                        <a class="my_cart" title='购物车'>
                          <i class=" icon-shopping-cart icon-white shopping_cart"></i>
                          <span id="cart_total" class="cart-total"></span>
                        </a>
                        <div class="cart_info_dropdown hide">
                          
                        </div>
                    </div>
                    <div class='topbar_history'>
                      <a href='/management/order'>历史订单</a>
                    </div>
                <?php 
                  } ?>
            </div>
      </div>
    </div>
   
 
    <script src='/js/helper/helper_function.js'></script>
    <script type="text/javascript">
      function show_cart_num(cart) {
        if (cart.length == 0)
            $('#cart_total').text("");
        else
            $('#cart_total').text(cart.length);
      }
      $(document).ready(function(){
          var cart=get_cart();
          show_cart_num(cart);
          var v = 0;
          $(document).click(function(e) {
            if (!$(e.target).is('.cart_info_dropdown, .cart_info_dropdown *')) {
              if ($(e.target).parent().hasClass('my_cart'))
                $('.cart_info_dropdown').toggleClass('hide');
              else
                $('.cart_info_dropdown').addClass('hide');
            }
          });
          $('.my_cart').click(function() {
            var cart = get_cart();
            show_cart_num(cart);
            if (cart.length == 0) {
              $('.cart_info_dropdown').html("您的框都是空的，去来易份");
              return;
            }
            $('.cart_info_dropdown').html("");
            for (var i in cart) {
              var h="<div class='cart_store_one'> \
                      <div class='cart_head'> \
                          <a class='cart_t black'href='/store/"+cart[i].university_id+"/"+cart[i].store_id+"'>"+
                            cart[i].store_name+
                          "</a> \
                      </div> \
                      <div class='cart_food'>";
                        for (var k in cart[i].blanket) {
                          for (var j in cart[i].blanket[k].food) {
                            h += "<div class='cart_one_food'>"+
                                    cart[i].blanket[k].food[j].food_name
                                    +"(￥"+cart[i].blanket[k].food[j].price+")"
                                    +"x"+cart[i].blanket[k].food[j].number+
                                 "</div>";

                          }
                        }
                h += "</div> \
                    </div>";
                $('.cart_info_dropdown').append(h);
            }
            $('.cart_info_dropdown').append("<div class='black_a'><a href='/check_out'>去结算</a></div>");
            
          });
      });
    </script>
    
    
