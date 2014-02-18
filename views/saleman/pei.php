<div class='m_right'>
	<h5>查看超时白吃</h5>
	<?php
	if (@$empty) {
		echo "<div>暂时没有超时白吃</div>";
	}
	else {
		foreach ($pei as $row) { ?>
			<div>
				<span class='black_a'>订单号：<a href='/saleman/sale_details/<?=$row['saleId']?>' target='_blank'><?=$row['saleId']?></a></span>
				<span>请求时间：<?=$row['pei_time']?></span>
				<span><a sale='<?=$row['saleId']?>'class='btn btn-success finished' title='完全联系好才行完成请求'>完成请求</a></span>
			</div>
		<?php
		} 
	} ?>
	<script type="text/javascript">
	$('.finished').click(function() {
		alert('确认完成请求吗？');
		var sale_id = $(this).attr('sale');
		$.ajax({
			type: "get",
			url: "/saleman/pei_finished/"+sale_id,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
	        dataType: "json",
	        success: function(d){
	        	if (d.state == 1) {
	        		alert('完成超时白吃成功');
	        		window.location.reload();
	        	}
	        	else
	        		alert('完成超时白吃失败');
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
