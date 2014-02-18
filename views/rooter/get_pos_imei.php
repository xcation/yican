<div class='m_right'>
	<?php
	if (count($data) == 0)
		echo "还没有收到任何消息";
	else { ?>
		<div>
			<span>手机号码：<?=$phone?></span>
			<span>内容：<?=$content?></span>
			<span>时间：<?=$time?></span>
		</div>
	<?php
	}  ?>
</div>
</div>
</div>