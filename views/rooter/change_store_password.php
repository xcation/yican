<div class='m_right'>
	<?php
		if (@$input_a_store) { ?>
			<form action='/rooter/change_store_password' method='post'>
				请输入商店编号<input type='text' name='store_id'/>
				<input type='submit' value='确认'/>
			</form>
		<?php
		}
		else { ?>
			<div>你正在修改<?=$store_name?>的密码</div>
			<div>请确保密码长度在6位以上</div>
			<form action='/rooter/change_store_password/<?=$store_id?>' method='post'>
				输入新密码<input type='password' name='passwd_1'/>
				确认新密码<input type='password' name='passwd_2'/>
				<input type='submit' class='btn' value='提交' />
			</form>
		<?php
		}
	?>
	<script type="text/javascript">
		<?php
			if (@$alert) {
				if (@$success) { ?>
					alert('修改成功');
				<?php
				}
				else { ?> 
					alert('修改失败。两次密码不一致，或者长度小于6位');
		<?php
				}
		} ?>
	</script>
</div>
</div>
</div>