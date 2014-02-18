<script type="text/javascript" src='/js/unslider.js'></script>
<div class='main'>
  <div>
    <div class='sun'></div>
  </div>
    <div class='find_food'>
      <div class='q_t_intro'>今天吃什么？随机出现卖的最好的菜哦</div>
      <div id="quick_find" class='modal hide fade' tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-header black_a">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <a class='today'data-placement="top" data-toggle="tooltip"
               data-original-title="今天吃什么的二十张图片是昨日的前十销量榜加十张随机食物图片产生的，点问号试试吧！">今天吃什么</a>
          </div>
          <div class="modal-body">
            <div class="banner black">
              <ul>
                <?php
              foreach ($img as $key=>$val) { ?>
                <li>
                    <a>
                      <img class="random_food" src="<?php if (@$val['food_img_src'])
                                                              echo "/img/food/{$val['food_img_src']}";
                                                          else
                                                              echo "/img/logo.png";?>" >
                    </a>
                    <div class="random_food_intros">
                        <div class='q_food_left'>
                          <div class='quick_find_left'title='<?=@$val['storeName']?>'><?=@$val['storeName']?></div>
                          <div class='quick_find_left'title='<?=@$val['food_name']?>'><?=@$val['food_name']?></div>
                          <div>
                              单价：<span><?=$val['price']?></span>
                          </div>
                          <div class='q_food'>
                              昨日销量：<span><?=@$val['daily_sale']?></span>
                          </div>
                        </div>
                        <div class='q_food_right'>
                          <form action="/store/<?=$university_id?>/<?=@$val['store_id']?>/#food-<?=@$val['food_id']?>" method='post'>
                            <input type='submit' class='btn btn-warning'value='<?php
                              if ($val['state'] != '0')
                                echo "休息中";
                              else
                                echo "来易份"; ?>'
                              <?php
                              if ($val['state'] != '0')
                                echo "disabled";?> class='btn another_one'></input>
                              <input type='hidden' name='food' value="<?=$val['food_id']?>">
                          </form>
                        </div>
                    </div>
                </li>
                <?php } ?>
              </ul>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">返回</button>
          </div>
      </div>
      <div class='question_mark'>
          <a class='q_mark' title='点我看看嘛' data-toggle="modal"data-target='#quick_find'>?</a>
      </div>

      <script type="text/javascript">
        $('.today').tooltip();

        $('#quick_find').modal({
          backdrop: false,
          keyboard: true,
          show:false
        });
        $('#quick_find').on('shown', function() {
            $('.banner').unslider({
                speed: 500,               //  The speed to animate each slide (in milliseconds)
                delay: 10,              //  The delay between slide animations (in milliseconds)
                // complete: function() {},  //  A function that gets called after every slide animation
                keys: true,               //  Enable keyboard (left, right) arrow shortcuts
                dots: true,               //  Display dot navigation
                fluid: false              //  Support responsive design. May break non-responsive designs
            });
        });
        </script>
    </div>
    <div class='announcement'>
      <h4 class='announce_title'>易餐公告</h4>
      <div id='announce'>
        <ul>
            <?php
              if (count($announce) > 0) {
                foreach($announce as $row) { ?>
                  <li>
                    <div class='announce_content'><?=$row['announce_content']?></div>
                    <div class='announce_time'><?=date("Y-m-d", strtotime($row['createTime']))?></div>
                  </li>
                <?php
                }
              }?>
        </ul>
        <script type="text/javascript">
        $(function() {
            $('#announce').vTicker({
                speed: 8000,     //滚动速度，单位毫秒。
                pause: 4000,        //暂停时间，就是滚动一条之后停留的时间，单位毫秒。
                showItems: 10,      //显示内容的条数。
                animation: 'fade',  //动画效果，默认是fade，淡出。
                mousePause: false,   //鼠标移动到内容上是否暂停滚动，默认为true。
                height: 305,        //滚动内容的高度。
                direction: 'up'     //滚动的方向，默认为up向上，down则为向下滚动。
                });
            });
        </script>
      </div>
    </div>
    <div class="stores blank">
      <div class="head_container">
        <?php
        foreach ($block_info as $block) { ?>
        <div class='region_one'>
          <div class='store_line_one'>
            <div class='food_region_name store_f'>
              <?php
              if (2 == $block['block_num']) { ?>
                <a id='order_food'data-placement="top"data-toggle="tooltip"
                   data-original-title="接受预定就是：提供网上订餐，到店就餐的服务">
                   <?=$block['block_name']?>
                </a>
              <?php
              }
              else {
                echo $block['block_name'];
              } ?>
            </div>
            <div class='d_open_or_not store_f'>
              <input block="<?=$block['block_num']?>"class='open_choise'type="checkbox" name="filter" block="<?=$block['block_num']?>"style='margin:2px 0 0'value="open" />
              <label class="open_or_not" block="<?=$block['block_num']?>">营业中</label>
            </div>
            <div class="m_taste dropdown store_f">
              <a class="dropdown-toggle taste_choice" data-toggle="dropdown" block="<?=$block['block_num']?>">
                <span value="0">选个味道</span>
                <b class="caret"></b>
              </a>
              <ul class="tastes_type dropdown-menu">
                <li><a class="distinct_tastes" value="0">选个味道</a></li>
                <?php
                  $count = 0;
                  foreach ($store_type as $val) { ?>
                    <li><a class="distinct_tastes" value="<?=++$count?>"><?=$val['storeTypeName']?></a></li>
                  <?php } ?>
              </ul>

            <script type="text/javascript">

              $('#order_food').tooltip();
              var university_id = '<?=$university_id?>'
              function _get_block(o) {
                  return o.attr('block');
              }
              function _get_store_info(i) {
                var checked=0;
                $(":checkbox[name='filter'][checked][block='"+i+"']").each(function() {
                  checked = 1;
                });
                var taste = $('.taste_choice[block='+i+']').children('span').attr('value');
                var deliver_cost = $('.qisongjia_choice[block='+i+']').children('span').attr('value');
                var i = i;
                $.ajax({
                    type:'GET',
                    dataType: 'html',
                    url: '/university/ajax_get_store_info/'+i+'/'+university_id+'/'+checked+'/'+taste+'/'+deliver_cost,
                    beforeSend:function(data) {
                        $('.restaurant[block='+i+']').html("<div style='margin-left:440px'><img src='/img/big_loading.gif'></img></div>");
                    },
                    success: function(data) {
                        $('.restaurant[block='+i+']').html(data);
                    },
                    error:function(data) {
                      alert('网络出错');
                    }
                });
              }

              $(".m_taste").mouseleave(function(){
                $(".m_taste").removeClass('open');
              });

              $(".distinct_tastes").click(function() {
                var span = $(this).parent().parent().prev().children('span');
                span.text($(this).text());
                span.attr('value', $(this).attr('value'));
                _get_store_info(_get_block($(this).parent().parent().prev()));
              });
              $('.open_or_not').click(function(){
                var c_b = $(this).prev();
                if (c_b.attr['checked'])
                  c_b.removeAttr('checked');
                else
                  c_b.attr('checked', 'checked');

                _get_store_info(_get_block($(this)));
              });
              $(":checkbox[name=filter]").click(function() {
                _get_store_info(_get_block($(this)));
              });
            </script>
            </div>
            <div class="qisongjia dropdown store_f">
              <a class="dropdown-toggle qisongjia_choice" data-toggle="dropdown" block='<?=$block['block_num']?>'>
                <span value="0">起送价</span>
                <b class="caret"></b>
              </a>
              <ul class="qisongjia_type dropdown-menu">
                <li><a class="distinct_qisongjia" value="0">起送价</a></li>
                <li><a class="distinct_qisongjia" value="10">10元以内</a></li>
                <li><a class="distinct_qisongjia" value="20">20元以内</a></li>
                <li><a class="distinct_qisongjia" value="30">30元以内</a></li>
              </ul>

              <script type="text/javascript">

                $(".qisongjia").mouseleave(function(){
                  $(".qisongjia").removeClass('open');
                });
                $(".distinct_qisongjia").click(function() {
                  var span = $(this).parent().parent().prev().children('span');
                  span.text($(this).text());
                  span.attr('value', $(this).attr('value'));
                  _get_store_info(_get_block($(this).parent().parent().prev()));
                });
              </script>
            </div>
        </div>
        <div class="restaurant" block="<?=$block['block_num']?>">
            <?php
              // 表示订餐
              $i = $block['block_num'];
              include("restaurant_body.php");
            ?>
        </div>
    </div>
    <?php
    } ?>
    <div class='rest_blank'></div>

    </div>
  </div>
</div>