<div class='m_right'>
	<h5>当前短信服务商: </h5><?=$smsSender?>
	<h5>当前短信禁用状况: </h5><?php if ($disableSms == 1) echo "禁用了!"; else echo "使用中"; ?>
	<form action='/rooter/sms_configure' method='post'>
		<h5>设置通道</h5>
		<?php
		foreach($all_sms_sender as $row) { ?>
			<?=$row['name']?><input type='radio' name="modify_sender" value="<?=$row['value']?>" />
		<?php } ?>
		<div>
			禁用短信<input type='radio' name='disable_sms' value='1' />
		</div>
		<input type='submit' value='提交' />
	</form>
</div>
</div>
</div>


