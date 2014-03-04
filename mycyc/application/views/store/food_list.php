<?php
function echo_state($state) {
	if (!$state)
		echo "休息中";
	else
		echo "来易份";
}

?>

<div id="store-header">
	<?=$store_name?>--食物列表|<a href="<?=constant("mycycbase")?>/store/info/<?=$university_id?>/<?=$store_id?>">店铺信息</a>
</div>
<script src="<?=constant("mycycbase")?>/js/jquery.cookie.js"></script>
<script src="<?=constant("mycycbase")?>/js/bootstrap.js"></script>
<div id="button-wrapper">
	<form action="<?=constant("mycycbase")?>/cart" method="post" id="order-form">
		<button class="btn btn-default btn-sm" id="confirm-button">去提交订单</button>
		<a class="btn btn-default btn-sm" id="reset-button" href='<?=constant('mycycbase')?>/store/reset_order/<?=$university_id?>/<?=$store_id?>'>重置所有</a>
		<input type='hidden' name='now_university' value='<?=$university_id?>'/>
		<input type='hidden' name='now_store' value='<?=$store_id?>'/>
		<input type="hidden" name="order" id="hidden-input" />
	</form>
	<div>
		<?php
		if (@$old_store) { ?>
			<a href="<?=constant('mycycbase')?>/store/<?=$old_university?>/<?=$old_store?>">回到<?=$old_store_name?></a>
			<a href="<?=constant('mycycbase')?>/store/reset_all/<?=$university_id?>/<?=$store_id?>">清空<?=$old_store_name?></a>
		<?php
		} ?>
	</div>
</div>
<div id="hint"></div>

<div id="food-category-wrapper">
	<ul id="food-category">
		<?php
			foreach($food_type as $val) {?>
				<li><a href="#category-<?=$val['foodTypeId']?>"><?=$val['foodTypeName']?></a></li>
		<?php
			}
		?>
	</ul>
</div>	

<div class="food_info">
	<?php
		foreach($food_type as $val) { 
	?>
	        <div class='section'id='category-<?=$val['foodTypeId']?>'>
				<div class="food_type"><p><?=$val['foodTypeName']?></p><a class="top" href="#header">回到顶部</a></div>
				<div class="my_food">
				<?php
					$my_food = $food_info[$val['foodTypeId']]; 
					if (count($my_food['no_img']) > 0) { 
						echo "<table>";
						foreach ($my_food['no_img'] as $v) { 
				?>
							<tr class='food_no_img' id='food-<?=$v['foodId']?>'>
								<td class='no_f_name'>
									<?=$v['foodName']?><br />
									<?=$v['note']?>
								</td>
								<td class='no_f_price'>￥<?=$v['price']?></td>

								<td class='no_f_btn'>
									<a <?php if (!$state) echo "disabled"?> class='btn btn-success btn-xs food_no_img_line order-one' 
										food-id='<?=$v['foodId']?>'
										food-name='<?=$v['foodName']?>'
										food-price='<?=$v['price']?>' 
										href="<?=constant('mycycbase')?>/store/new_food/<?=$university_id?>/<?=$store_id?>/<?=$v['foodId']?>">
										<?php echo_state($state); ?>
									</a>
								</td>
								<td class="no_f_symbol" food-id='<?=$v['foodId']?>'></td>
							</tr>
				<?php
						}
						echo "</table>";
					}
				?>
					</div>
				</div>
	<?php 
		}
	?>
</div>

<script type="text/javascript">
order = JSON.parse($.cookie('mycyc_order'));
if(!order)
	order = new Array();
for(i=0; i<order.length; i++)
	$(".no_f_symbol[food-id='"+order[i]['foodId']+"']").append("<span class='glyphicon glyphicon-ok'></span>");

<?php
	if (@$old_store) { ?>
		alert("您在<?=$old_store_name?>有订单,如果下单就必须清空哦!注意点击最上面");
	<?php
	} ?>
</script>