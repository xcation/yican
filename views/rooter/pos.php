<div class='m_right'>
	<h3>初始化pos机</h3>
	<form action='/rooter/pos' method='post'>
		<label>请输入商店pos机对应的手机号码：</label><input name='telephone' type='text' />
		<h5>确认打印机硬件配置好的情况下点击初始化打印机</h5>
		<input type='submit' value='初始化' />
		<a class='black'href="/rooter/get_pos_imei/<?=$telephone?>" target='_blank'>点击获得imei号，需要等几秒，可以刷新</a>
	</form>
<div>-------------------------------------------------------------------</div>
	<h5>设置商户的pos机</h5>
	<form action='/rooter/pos' method='post'>
		请输入商店id<input type='text' name='store_id'/>
		输入打印机imei<input name='imei' type='text' />
		<input type='submit' value='设置imei' />
	</form>
<div>-------------------------------------------------------------------</div>
	<h5>取消商店的pos机应用</h5>
	<form action='/rooter/pos' method='post'>
		<label>请输入商店id</label>
		<input type='hidden' value='1'name='cancel'/>
		<input type='text' name='store_id'/>
		<input type='submit' value='确认取消' />
	</form>
	<script type="text/javascript">
		<?php
		if (@$error) { ?>
			alert('短信发送出错');
		<?php
		} 
		else if (@$post_tel) { ?>
			alert('初始化成功，请点击获取imei');
		<?php
		} 
		else if (@$cancel) { ?>
			alert('取消成功');
		<?php
		}
		else if (@$success) { ?>
			alert("设置商店pos成功");
		<?php	
		} ?>
	</script>
</div>
</div>
</div>
