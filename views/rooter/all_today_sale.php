<div class='m_right'>
	<h3>查看今日订单详细</h3>
	<?php
	foreach ($region as $row) { ?>
		<h4>这是<?=$row['region_name']?>的详情</h4>
		<h4>订单总数：<?=$row['region_info']['today_sale_num']?></h4>
		<h4>订单总额：<?=$row['region_info']['today_sale_money']?></h4>
		<h4>催单数量：<?=$row['region_info']['urgent_num']?></h4>
		<hr />
	<?php
	} ?>

</div>
</div>
</div>