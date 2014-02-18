<table class="restaurant-table">
<tbody>
  <?php
    $count = 0;
    // var_dump($i);
    // var_dump($store_info);
    if (count($store_info) == 0 || count($store_info[$i]) == 0)
      echo "<div style='margin-left:440px;color:#222;width:300px;'>没有对应的结果</div>";
    else {
      foreach ($store_info[$i] as $val) {
        if ($count % 5 == 0) { ?>
          <tr>
        <?php } ?>
        <td class="td-one-restaurant">
          <div class="one-restaurant <?=$val['state_choise']?>">
              <div class="rest-left">
                <a href="/store/<?=$university_id?>/<?=$val['storeId']?>/" target='_blank'>
                    <img class="rest-logo" src="<?php if (@$val['imgLoc']) 
                                                          echo "/img/store/{$val['imgLoc']}";?>" />
                </a>
              </div>
              <div class="rest-right">
                <p class='rest-in'>
                    <a href="/store/<?=$university_id?>/<?=$val['storeId']?>/" target='_blank'>
                      <?=$val['storeName']?>
                    </a>
                </p>
                <p class='rest-in' title='<?=$val['each_store_type']?>'>特色：<?=$val['each_store_type']?></p>
                <p class='rest-in'><span class='left'>月销量：<?=$val['total_buyer_month']?></span><span class='right'>评价：<?php echo sprintf("%.1f", $val['avg_score_month']);?></span></p>
                <p class='rest-in'><span class='left'>起送价：<?=$val['delivery_cost']?></span><span class="rest-state right"><?=$val['state']?></span></p>
                
              </div>
          </div>
        </td>
        <?php if ($count % 5 == 4) { ?>
          </tr>
        <?php }
        $count++;
      }
    } ?>
</tbody>
</table>
<script type="text/javascript">
          
          // $('.one-restaurant').mouseover(function() {
          //   var o = $(this).next();
          //       o.removeClass('hide');
          // });
          // $(".td-one-restaurant").mouseleave(function() {
          //   var o = $(this).children().first().next();
          //       o.addClass('hide');
          // });
        </script>