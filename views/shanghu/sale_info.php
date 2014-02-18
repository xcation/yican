<div class='s_right'>
	<h3>查看交易记录</h3>
	<div>
		<form action='/shanghu/sale_info' method='post'>
			<select name='sale_month'>
				<option value='0' selected>这个月</option>
				<option value='1'>上个月</option>
				<option value='2'>两个月前</option>
			</select>
			<input type='submit' value='查询'class='btn'/>
		</form>
		<div>
			<?php
			$month_sale_sum = 0;
			foreach($sale_info as $date) { ?>
				<div>
					<h4><?=$date['createDate']['createDate']?></h4>
					<?php
					$daily_sale_sum = 0;
					foreach($date['createDate']['saleId'] as $sale_id) { ?>
						<h5><?=$sale_id['saleId']['saleId']?></h5>
						<?php
						foreach($sale_id['saleId']['foodInfo'] as $food) {
							$daily_sale_sum += $food['price'] * $food['num']; ?>
							<p><?=$food['foodName']?>(<?=$food['price']?>)x<?=$food['num']?></p>
						<?php
						} ?>
					<?php
					} ?>
					<p>日销量<?=$daily_sale_sum?></p>
				</div>
			<?php
				$month_sale_sum += $daily_sale_sum;
			} ?>
			<p><?=$month_sale_sum?></p>
		</div>
	</div>
</div>
</div>
</div>
