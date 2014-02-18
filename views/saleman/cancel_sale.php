<div class='m_right'>
	<h5>查看商户提交的非法订单</h5>
	<?php
		foreach($cancel_info as $row) { ?>
			<table>
				<tbody>
					<tr>
						<td>订单号：<?=$row['saleId']?></td>
						<td>买家ID：<?=$row['buyerId']?></td>
						<td>卖家编号：<?=$row['storeId']?></td>
					</tr>
					<tr>
						<td>卖家店名：<?=$row['storeName']?></td>
						<td>大学：<?=$row['university_name']?></td>
						<td>订单提交时间：<?=$row['createTime']?></td>
					</tr>
					<tr>
						<td>卖家提交请求时间：<?=$row['cancel_post_time']?></td>
						<td>提交理由：<?=$row['cancel_reason']?></td>
						<td>买家地址：<?=$row['university_name']?></td>
					</tr>
					<tr>
						<td>买家长号：<?=$row['user_l_tel']?></td>
						<td>买家短号：<?=$row['user_s_tel']?></td>
						<td>卖家联系方式：<?=$row['phone']?></td>
					</tr>
				</tbody>
			</table> 
			
			<?php
			$sum = 0;
			foreach($row['sale_details'] as $details) { 
				$sum += $details['price']*$details['num']; ?>
				<div>
					<span>食物名字：<?=$details['foodName']?></span>
					<span>食物价格：<?=$details['price']?></span>
					<span>数量：<?=$details['num']?></span>
				</div>
			<?php
			} ?>
			<div>
				<h6>确认订单结果</h6>
				<a class='btn btn-success' href='/saleman/deal_cancel/1/<?=$row['saleId']?>'>取消成功</a>
				<a class='btn btn-warning' href='/saleman/deal_cancel/0/<?=$row['saleId']?>'>取消失败</a>
			</div>
		<?php
		}
		?>	
<script type="text/javascript">
	<?php
		if (@$confirm) { ?>
			alert('确认成功');
	<?php
	} ?>
</script>
</div>
</div>
</div>
