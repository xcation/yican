<div class='m_right'>
	<h5>查看pos机问题</h5>
	<?php
	if (count($pos_error) == 0) {
		echo "<div>暂时没有pos机问题</div>";
	}
	else { 
		echo "<div>下面的店已经超过10分钟在正常营业时间内pos关闭了</div>";
		foreach ($pos_error as $row) { ?>
			<div>
				<span>商店ID：<?=$row['store_id']?></span>
				<span>商店名字：<?=$row['store_name']?></span>
				<span>联系电话_1：<?=$row['tel_1']?></span>
				<span>联系电话_2：<?=$row['tel_2']?></span>
				<span>联系电话_3：<?=$row['tel_3']?></span>
				<a class='btn btn-warning finished' store="<?=$store_id?>">确认处理完毕</a>
			</div>
		<?php
		} 
	} ?>
	<script type="text/javascript">
	$('.finished').click(function() {
		alert('确认完成处理吗？');
		var store_id = $(this).attr('store');
		$.ajax({
			type: "get",
			url: "/saleman/pos_error_finished/"+store_id,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
	        dataType: "json",
	        success: function(d){
	        	if (d.state == 1) {
	        		alert('pos机问题处理成功');
	        		window.location.reload();
	        	}
	        	else
	        		alert('pos机问题处理失败');
	        },
	        error: function(){
	        	alert('网络错误，请刷新后再试');
	        }
		});
	});
	</script>

</div>
</div>
</div>
