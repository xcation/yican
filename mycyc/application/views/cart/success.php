<div>
<?php
	if (@$not_fill) { ?>
		<script type="text/javascript">
		alert('信息没有填完整哦！或者长号要是11位啦');
		window.location.href="<?=constant('mycycbase')?>/cart";
		</script>
	<?php
	}
	if ($re['state'] == 1) { ?>
		订单提交成功，订单号为<?=$sale_id?><br />
		欢迎下次再来一餐易餐，<a href='<?=constant('mycycbase')?>/order'>查看订单</a>
	<?php
	}
	else {
		echo "订单失败了～～";/*$re['error'];*/
	}
	?>
</div>