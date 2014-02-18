<div class='m_right'>
	<a href="/rooter/sms_caixuntong_chargeup" class='red'>财讯通充值</a>
	<h5>最新100条短信</h5>
	<table>
		<thead>
			<tr>
				<th>接收者</th>
				<th>内容</th>
				<th>发送人</th>
				<th>时间</th>
				<th>是否发出</th>
			</tr>
		</thead>
		<tbody>
	<?php
		foreach (@$sms_info as $row) { ?>
			<tr>
				<td><?=$row['msgPhone']?></td>
				<td><?=$row['msgContent']?></td>
				<td><?php
					if ($row['msgSender'] == 1)
						echo "短信宝";
					else if ($row['msgSender'] == 2)
						echo "创世华信";
					else if ($row['msgSender'] == 3)
						echo "财迅通";
				?></td>
				<td><?=$row['sendTime']?></td>
				<td><?php
					if ($row['is_disabled'])
						echo "N";
					else
						echo "Y";
				?></td>
			</tr>
	<?php }
	?>
		</tbody>
	</table>
</div>
</div>
</div>


