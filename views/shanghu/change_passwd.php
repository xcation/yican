<div class='s_right'>
	<div class='red'>
	<?php
	if (@$error)
		echo $error;
	if (@$success)
		echo $success;
	?>
	</div>
	<form action='/shanghu/change_passwd/<?=$store_id?>' method='post'>
		输入新密码<input type='password' name='passwd_1'/>
		确认新密码<input type='password' name='passwd_2'/>
		<input type='submit' class='btn' value='提交'>
	</form>
</div>

</div>
</div>
