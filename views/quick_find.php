    <div class="quick_find">
      <div id="myCarousel" class="carousel slide">
        <div class="carousel-inner">
          
          <?php 
          foreach ($img as $key=>$val) { ?>
            <div class="item 
            <?php
              if ($key == 0)
                echo 'active';?>">
              <a href="/store/<?=$university_id?>/<?=@$val['store_id']?>/#food-<?=@$val['food_id']?>">
                <div class="random_food_pic">
                  <img class="random_food" src="/img/food/<?=@$val['food_img_src']?>">
                </div>

                <div class="random_food_intros">
                  <span><?=@$val['storeName']?></span>
                  <span><?=@$val['food_name']?></span>
                  <span><?=@$val['price']?></span>
                  <div>
                    昨日销量<span><?=@$val['daily_sale']?></span>
                  </div>
                </div>
              </a>
            </div>
            
            <?php } ?>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
      </div>

      <script type="text/javascript">
        $('.carousel').carousel('pause');
        $('.carousel').carousel({
          interval: 1500
        });
        $('.find_random').click(function() {
          $('.carousel').carousel('pause');
        });
      </script>
    </div>