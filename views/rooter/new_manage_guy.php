<div class='m_right'>
	<h3>新建区域人员管理</h3>
	<form action="/rooter/new_manage_guy" method='post'>
		<select name="guy_type" id="">
			<option value="1">店铺创建者</option>
			<option value="2">日常事务处理者（业务员）</option>
		</select>
		<label for="">用户名</label>
		<input type="text" name='userid'>
		<label for="">密码</label>
		<input type="password" name='password'>
		<label for="">再次输入密码</label>
		<input type="password" name='password_again'>
		<label for=""></label>
		<input type="submit" value="提交">
	</form>
	<script type='text/javascript'>
		<?php
		if (@$msg) { ?>
			alert('<?=$msg?>');
		<?php } ?>
	</script>
</div>
</div>
</div>