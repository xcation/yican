<div class='m_right'>
<h4>选择订单时间</h4>
<form action='/rooter/sale_history/' method='post'>
	<select name='sale_month'>
		<option value='0' selected>这个月</option>
		<option value='1'>上个月</option>
		<option value='2'>两个月前</option>
	</select>
	<input type='submit' value='查询'class='btn'/>
</form>
<?php
	if (@$all_history) { ?>
		<h4>这是<?=$this_month?>的所有订单</h4>
	<?php
		foreach (@$all_history as $row) { ?>
			<h4>这是<?=$row['region_name']?>的订单</h4>
		<?php
			if (@$row['all_store']) { ?>
				<strong>总订单量：<?=$row['sale_counter']?>&nbsp;总销售额：￥<?=$row['all_money']?></strong>
				<?php
				foreach ($row['all_store'] as $one_store) { ?>
					<h5>
						<table>
							<tbody class='r_sale_detail'>
								<tr>
									<td>商店编号：<?=$one_store['storeId']?></td>
									<td>商店名称：<?=$one_store['storeName']?></td>
								</tr>
								<tr>
									<td>商店电话：<?=$one_store['telephone']?></td>
									<td class='r_m_sale'>这个月总销售额：￥<?=$one_store['money']?></td>
									<td><a href='/rooter/store_sale_details/<?=$sale_month?>/<?=$one_store['storeId']?>' target='_blank'class='btn btn-warning'>查看详情</a></td>
									<td><a href='/rooter/print_sale/<?=$sale_month?>/<?=$one_store['storeId']?>' target='_blank' class='btn btn-warning'>下载商店月账单</a></td>
								</tr>
							</tbody>
						</table>
					</h5>
				<?php
				}
			}
		}
	} ?>
</div>
</div>
</div>