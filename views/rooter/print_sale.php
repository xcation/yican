<?=$store_name?>
<?php
	$one_store = $one_store['sale'];
	foreach ($one_store as $row) { ?>
		<div>
			日期：<?=$row['createDate']?>
		</div>
		<div class='r_d_sale_content'>
			<?php
			if (count($row['sale_id']) == 0) {
				echo "无";
			}
			else {
				foreach ($row['sale_id'] as $row_2) { ?>
					<strong><?=$row_2['saleId']?></strong>
					<div>
						<?php
						foreach($row_2['food_info'] as $row_3) { ?>
								<?=$row_3['foodName']?>&nbsp;&nbsp;&nbsp;<?=$row_3['num']?>&nbsp;&nbsp;&nbsp;<?=$row_3['price']?>&nbsp;&nbsp;||
						<?php
						} ?>
					</div>
				<?php
				} 
			} ?>
		</div>
	<?php
	} ?>