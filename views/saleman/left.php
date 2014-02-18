<div class='main'>
	<div class='head_container black'>
		<div class='sale_man_left black_a'>
			<?php
			if (@$region_manage) { ?>
			<a href="/saleman/cancel_sale">查看非法订单</a>
			<a href="/saleman/urgent">查看催单</a>
			<a href="/saleman/pei">查看超时白吃</a>
			<?php
			} ?>
			<a href="/saleman/telephone_search">用手机号查订单</a>
			<a href="/saleman/sale_id_search">用订单号查订单</a>
			<a href="/saleman/lottory" target="_blank">抽奖</a>
			<?php
			if (@$super_root) { ?>
			<a href="/saleman/pos_error">查看pos机错误</a>
			<?php } ?>
		</div>

