<div class="m_right">
	<h3>增加区域分类(最多使用11个区域)</h3>
	<div>
		<span>已有的区域:</span>
		<ul>
			<?php
			foreach ($block_info as $block) { ?>
				<li>
					<input type='text' value="<?=$block['block_name']?>"/>
					<a class='blue modifyBlock' bid="<?=$block['block_num']?>">修改</a>
				</li>
			<?php
			} ?>
		</ul>
		<form action='/rooter/new_store_type' method='post'>
			<label>增加区域</label>
			<input type='text' name='new_block' />
			<input type='submit' value='提交'>
		</form>
	</div>
	<script type='text/javascript'>
		$('.modifyBlock').click(function() {
		var id = $(this).attr('bid');
		var block_name = $(this).prev().val();
		$.ajax({
			type: "get",
			url: "/rooter/ajax_new_store_type/1/"+id+'/'+block_name,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
	        dataType: "json",
	        success: function(d){
	        	if (d.state == 1)
	        		alert('成功修改区域');
	        	else
	        		alert('失败');
	        },
	        error: function() {
	        	alert('网络错误，请刷新后再试');
	        }
		});
	});
	</script>
</div>
</div>
</div>
