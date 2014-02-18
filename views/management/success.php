<div class='main'>
	<div class='head_container blank'>
		<div class='black'>
			<?php
			if (@$error) { ?>
				<h3 class="m_success">订单失败</h3>
				<p>
					暂时无法联系商家<span class='red'><?=$error?></span>，十分抱歉，一餐易餐会立刻与商家联系，对您造成的不便请您谅解。
				</p>
			<?php
			}
			else { ?>
				<h3 class="m_success">订单成功</h3>
				<p>您的订单已经成功，订单信息已经发向商家，外卖立马就到，准备好零钱哦，如果您使用的是一餐易餐预定功能，商家会马上与您联系</p>
			<?php
			} ?>
			<span class='black_a span_i_b'>
				<a href='/management/order'>返回管理中心</a>
			</span>
			<span class='black_a'>
				<a href='/'>继续点菜</a>
			</span>
		</div>
	</div>
</div>