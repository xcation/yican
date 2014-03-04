<?php
if (@$food_order['state'])
	echo "<div>暂时还没有记录</div>";
else {
	$i = 0;
	foreach($food_order as $fo) { ?>
	<div <?php if ( ($i++ % 2) == 0) echo "class='pink'" ?> >
		<div class='o_line_one'>
			<div>
				<span class='o_store_name black_a'>
					<a href="<?=constant("mycycbase")?>/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>"><?=$fo['storeName']?></a>
					<?php
					if ($fo['validity'])
						echo "<span class='valid_sale'>有效";
					else
						echo "<span class='invalid_sale'>订单确认中";
					?>
				</span>
			</div>
			<div>
				<table class='black sale_table'>
					<tr>
						<td><strong>订单号：</strong><?=$fo['saleId']?></td>
						<td><strong>商店电话：</strong><?=$fo['contact_phone']?></td>
					</tr>
					<tr>
						<td><strong>您的地址：</strong><?=$fo['user_addr']?></td>
						<td><strong>您的电话：</strong><?=$fo['user_l_tel']?></td>
					</tr>
					<tr>
						<td><strong>您的备注：</strong><?=$fo['taste']?></td>
					</tr>
				</table>
				<form action='<?=constant("mycycbase")?>/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>' method='post'>
					<!-- <input type='submit'value='全部再来易份' class='btn another_one'></input> -->
					<input type='hidden' name='food' value="<?php
						   foreach ($fo['food_one_sale'] as $key => $food) {
						   		if ($key == 0)
					       			echo $food['foodId'];
					       		else
					       			echo "-".$food['foodId'];
					   	   } ?>">
				</form>
			</div>
		</div>
		<div class='o_line_two'>
			<?php
			$sum = 0;
			foreach($fo['food_one_sale'] as $food) { ?>
				<div class='m_food_s'>
					<span class='m_food_n' title='<?=$food['foodName']?>（￥<?=$food['price']?>）x <?=$food['num']?>'>
						  <?=$food['foodName']?>（￥<?=$food['price']?>）x <?=$food['num']?>
					</span>
					<?php
					if ($fo['validity']) { 
						$sum += $food['price']*$food['num'];
					} ?> 
					<span class='again_btn'>
						<form action='<?=constant('mycycbase')?>/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>/#food-<?=$food['foodId']?>'method='post' class='again_form f_right'>
							<input type='submit'value='再来易份' class='btn another_one'></input>
							<input type='hidden' name='food' value='<?=$food['foodId']?>'>
						</form>
					</span>
				</div>
			<?php
			} ?>
			<div class='m_food_total'>
				<span>总计：￥<?=$sum?></span>
			</div>
		</div>
	</div>
<?php }
} ?>
