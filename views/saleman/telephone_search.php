<div class='m_right'>
	<h5>根据手机号查询订单</h5>
	<form action='/saleman/telephone_search' method='post'>
		<input type='text' name='telphone'/> 
		<input type='submit' value='提交'/>
	</form>
	<?php
		if (@$post) { ?>
			<h6>这是根据手机号<?=$tel?>查询到的订单</h6>
			<?php
			foreach ($sale as $row) { ?>
				<div class='black'>
					<span class='black_a'>订单号：<a  target="_blank" href='/saleman/sale_details/<?=$row['saleId']?>'><?=$row['saleId']?></a></span>
					<span>订单时间：<?=$row['createTime']?></span>
					<span>订单地址：<?=$row['user_addr']?></span>
				</div>
			<?php
			}
		}
	?>
</div>
</div>
</div>
