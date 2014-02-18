<?php
	if (@$empty)
		echo "<div>查不到这个订单</div>";
	else { ?>
		<h4>订单号：<?=$sale['saleId']?>&nbsp;</h4>
		<strong><?php if ($sale['from_tel'] == '1')
						echo "来自电脑";
					  else
					  	echo "来自手机"; ?></strong>
		</br>
		<span>
			<div>
			买家地址：<strong><?=$sale['user_addr']?></strong>&nbsp;
			买家长号：<strong><?=$sale['user_l_tel']?></strong>&nbsp;
			买家短号：<strong><?=$sale['user_s_tel']?></strong>&nbsp;
			订单生成时间：<strong><?=$sale['createTime']?></strong>&nbsp;
			第一次催单时间：<strong><?=$sale['first_urgent_time']?></strong>&nbsp;
			</div>
			<div>
			卖家编号：<strong><?=$sale['storeId']?></strong>&nbsp;
			卖家名：<strong><?=$sale['storeName']?></strong>&nbsp;
			卖家联系方式：<strong><?=$sale['contact_phone']?></strong>&nbsp;
			卖家联系方式_1：<strong><?=$sale['contact_phone_1']?></strong>&nbsp;
			卖家联系方式_2：<strong><?=$sale['contact_phone_2']?></strong>&nbsp;
			</div>
		</span>
		<table class='black'>
			<thead>
				<tr>
					<th>食物名字</th>
					<th>价格</th>
					<th>数量</th>
				</tr>
			</thead>
			<tbody>
		<?php
		foreach($sale['food_info'] as $food) { ?>
			<tr>
				<strong>
					<td><?=$food['foodName']?></td>
					<td><?=$food['price']?></td>
					<td><?=$food['num']?></td>
				</strong>
			</tr>
		<?php
		} ?>
			</tbody>
		</table>
	<?php
	} ?>