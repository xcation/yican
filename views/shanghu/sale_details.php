<div class='s_right'>
	<h3>查看交易记录</h3>
	<div>
		<?php 
			include('choose_month.php');
		?>
		<div>
			日期：<?=$date?>
			<?php
			foreach($details as $sale_id) { ?>
				<h4>订单号：<?=$sale_id['saleId']?>&nbsp;交易金额：<?=$sale_id['sale_money']?></h4>
				<h6>
					买家地址：<?=$sale_id['user_addr']?>&nbsp;
					买家长号：<?=$sale_id['user_l_tel']?>&nbsp;
					买家短号：<?=$sale_id['user_s_tel']?>&nbsp;
				</h6>
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
				foreach($sale_id['foodInfo'] as $food) { ?>
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
		</div>
	</div>
</div>
</div>
</div>
