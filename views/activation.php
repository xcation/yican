<div class="main">
	<div class="head_container blank_a blank black">
		<?php
		if (@$error) { ?>
			<h3><?=$error?></h3>
		<?php
		}
		else { ?>
			<h3>激活成功</h3>
			<a href='/login'>回到登录界面</a>
		<?php
		} ?>	
	</div>
</div>