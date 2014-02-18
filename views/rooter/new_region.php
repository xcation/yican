<div class='m_right'>
	<form action="/rooter/new_region" method='post' enctype="multipart/form-data">
		<label>区域名称：（名字以空格分割）</label>
		<input type="text" name='region_name' />
		<label for="">设置区域图片</label>
		<input type='file' name='region_img'/>
		<label for="">设置区域管理员帐号：</label>
		<input type="text" name='userid' />
		<label for="">密码</label>
		<input type="password" name='password' />
		<label for="">再次输入密码</label>
		<input type="password" name='password_again' />
		<input type="submit" value='submit' />
	</form>
	<script type='text/javascript'>
		<?php
			if (@$success) { ?>
				alert('设置成功');
			<?php
			} else if (@$success === 0) { ?>
				alert('<?=@$msg?>');
			<?php
			} ?>
	</script>
</div>
</div>
</div>
