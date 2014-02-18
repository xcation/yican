<div class='m_right'>
	<div>
		<?php
			if (isset($info))
				echo $info;
		?>
	</div>
	<div>
		<span>已有的店铺种类：</span>
		<ul>
			<?php
			foreach ($store_type as $type) { ?>
				<li>
					<input type='text' value="<?=$type['storeTypeName']?>" >
					<a class='blue modifyStoreType' stid="<?=$type['storeTypeId']?>">修改</a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class='new_store_type'>
		<form action='/rooter/new_store_type' method='post'>
			<label>添加新种类</label>
			<input type='text' name='store_type'/>
			<input type='submit' value='提交'/>
		</form>
	</div>
	<div>
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
	</div>
	<script type="text/javascript">
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
		$('.modifyStoreType').click(function() {
			var id = $(this).attr('stid');
			var stname = $(this).prev().val();
			$.ajax({
				type: "get",
				url: "/rooter/ajax_new_store_type/0/"+id+'/'+stname,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
		        dataType: "json",
		        success: function(d){
		        	if (d.state == 1)
		        		alert('成功修改种类');
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