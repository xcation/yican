<div class='s_right'>
	<h5>取消非正常订单，我们的工作人员将在最多三个工作日内和您联系，对您造成的不便请您谅解</h5>
	<form action='/shanghu/cancel_sale/<?=$store_id?>' method='post'>
		<div>请输入订单号</div>
		<input type='text' name='sale_id'/>
		<div>请输入提交原因</div>
		<textarea colomn='20' row='5' name='reason'></textarea>
		<div><input type='submit' value='提交' class='btn' /></div>
	</form>
					
</div>
	<script type="text/javascript">
		<?php
			if (@$post) {  
				if ($success) { ?>
					alert('提交成功');
				<?php
				}
				else { ?>
					alert('<?=$error?>');
				<?php
				}
			} ?>
			
	</script>
</div>
</div>
