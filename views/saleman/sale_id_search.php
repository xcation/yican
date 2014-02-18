<div class='m_right'>
	<h5>根据订单号查询订单</h5>
	<form action='/saleman/sale_id_search' method='post'>
		<input type='text' name='sale_id'/> 
		<input type='submit' value='提交'/>
	</form>
	<?php
		if (@$post) { ?>
			<h6>这是根据订单号<?=$sale_id?>查询到的订单</h6>
			<?php
				include('sale_details_template.php');
		}
		?>
</div>
</div>
</div>
