<div class='s_right'>
	<h3>查看交易记录</h3>
	<div>
		<?php 
			include('choose_month.php');
		?>
		<?php
		if (@$error) { ?>
			<h4>出现未知错误</h4>
		<?php
		}
		else { ?>
			<div>
				<table class='black'>
					<thead>
						<tr>
							<th>交易日期</th>
							<th>成交额</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($sale_info as $date) { ?>
					<tr>
						<strong>
							<td class='black_a'>
								<a title='查看详细记录' class='sale_date'href='/shanghu/details/<?=$store_id?>/<?=$date['createDate']['createDate']?>'>
									<?=$date['createDate']['createDate']?>
								</a>
							</td>
							<td><?=$date['createDate']['money']?></td>
						</strong>
					</tr>
				<?php
				} ?>
					</tbody>
				</table>
			</div>
		<?php 
		}  ?>
	</div>
</div>
</div>
</div>
