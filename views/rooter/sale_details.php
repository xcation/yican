<h5>这是编号为<?=$store_id?>的商店在<?=$this_month?>的订单详情</h5>
<?php
	$one_store = $one_store['sale'];
	foreach ($one_store as $row) { ?>
		<div>
			日期：<?=$row['createDate']?>
		</div>
		<div class='r_d_sale_content'>
			<?php
			if (count($row['sale_id']) == 0) {
				echo "无订单";
			}
			else {
				foreach ($row['sale_id'] as $row_2) { ?>
					<strong>订单号：<?=$row_2['saleId']?></strong>
					<strong><?php if ($row_2['from_tel'] == '1') echo "来自电脑"; else echo "来自手机"; ?></strong>
					<?php
					foreach($row_2['food_info'] as $row_3) { ?>
						<div>
							食物名字：<?=$row_3['foodName']?>&nbsp;食物数量：<?=$row_3['num']?>&nbsp;食物价格：<?=$row_3['price']?>
						</div>
				<?php
					}
				}
			} ?>
		</div>
	<?php
	} ?>