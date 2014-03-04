<div class='black'>
	<?php
	if (!$valid) { ?>
		<h3 class="m_success">注册失败</h3>
		<p>
			$<?=$error?>
		</p>
		<span class='black_a span_i_b'>
			<a href='<?=constant('mycycbase')?>/register'>返回注册</a>
		</span>
	<?php
	}
	else { ?>
		<h3 class="m_success">注册成功</h3>
		2秒后浏览器将跳转回首页
		<script type="text/javascript">
			setTimeout(function(){window.location.href="<?=constant('mycycbase')?>";},2000);
		</script>
	<?php
	} ?>
	
	
</div>
