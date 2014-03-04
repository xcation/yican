<div id="c-location-change">
<script type="text/javascript">
	<?php
	if (@$new_addr) {
		if (@$new_res == 1) { ?>
			alert('新建成功');
			window.location.href = "<?=constant('mycycbase')?>/cart";
		<?php
		}
		else if (@$new_res == -1) { ?>
			alert('新建失败');
			window.location.href = "<?=constant('mycycbase')?>/cart";
		<?php
		} ?>
	<?php	
	}
	else if (@$delete) {
		if (@$is_deleted) { ?>
			alert('删除成功');
		<?php
		}
		else { ?>
			alert('删除失败！');
	<?php
		} ?>
		window.location.href = "<?=constant('mycycbase')?>/cart";
	<?php
	}
	else { 
		if (@$modi_res == 1)  { ?>
			alert('修改成功！');
			window.location.href = "<?=constant('mycycbase')?>/cart";
		<?php
		} 
		else if (@$modi_res == -1) { ?>
			alert('修改失败！');
			window.location.href = "<?=constant('mycycbase')?>/cart";
		<?php
		} ?>
	<?php
	}?>
</script>
	<?php
	if (@$new_addr || @$modi_pos) { ?>
	<h3 id="h-order-confirm"><?php if (@$new_addr) echo "新建地址"; else echo "修改您的地址";?></h3>
	<form action="<?=constant('mycycbase')?>/cart/<?php if (@$new_addr) echo "new_addr"; else echo "modify_addr"; ?>" method='post'>
		<input type='hidden' name="modi_pos" value='<?=@$modi_pos?>'/>
	    地址：<input type="text" name="addr" class="form-control"value="<?=@$addr?>"/>
	    长号：<input type="text" name="long_tel" class="form-control"value="<?=@$long_tel?>"/>
	    短号：<input type="text" name="short_tel" class="form-control"value="<?=@$short_tel?>"/>
		<input class="btn btn-primary" type='submit' value='提交'>
	</form>
	<?php
	} ?>
</div>