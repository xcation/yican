<div>
	<form action="/rooter" method='post'>
		<label for="" class='black'>用户名</label>
		<input type='text' name='ad_id'/>
		<label for="" class='black'>密码</label>
		<input type='password' name='ad_passwd'/>
		<label for="" class='black'>验证码</label>
		<p>
			<?php echo $cap; ?>
			<input type="text" name='code' />
		</p>
		<p>
			<input type='submit' value='登录' />
		</p>
	</form>
	<script type='text/javascript'>
		<?php if (@$fail) { ?>
			alert('<?=$msg?>');
		<?php
		} ?>
	</script>
</div>