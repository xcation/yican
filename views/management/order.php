<div class="main">
	<div>
		<div class='sun'></div>
	</div>
	<div class="head_container black">

		<div class="o_left">
			<div>
				<a class='black'href="/management/order">
					<?php if (!$login)
							  echo "查看最近一个月订单";
						  else
						  	  echo "查看所有订单";
					?>
				</a>
			</div>
			<?php
			if ($login) { ?>
				<div>
					<a class='black'href="/management/user"></a>
				</div>
			<?php
			}
			?>
		</div>
		<div class='o_right'>
			<?php
			// if (!$login) { ?>
				<div class='o_right_title'>
					<?php if (!$login)
							  echo "最近一个月的订单";
						  else
						  	  echo "所有订单";
					?>
				</div>
	      		<?php
	      		if (@$food_order['state']) {
					echo "<div>暂时还没有记录</div>";
				}
				else {
		      		foreach($food_order as $fo) { ?>
		      			<div class='o_line_one'>
		      				<div>
		      					<span class='o_store_name black_a'>
		      						<a href="/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>"><?=$fo['storeName']?></a>
		      						<?php
		      						if ($fo['validity'])
		      							echo "<span class='valid_sale'>有效";
		      						else
		      							echo "<span class='invalid_sale'>订单确认中";
		      						?>
		      					</span>
		      					<span class='f_urgent'>
		      						<a sale='<?=$fo['saleId']?>' class='urgent btn btn-success'<?php if (!$fo['time_up'])
		      															echo "disabled";?> title='十分钟后就可以催单了，间隔也是十分钟呢，三个小时后我们就不处理了哦'>我要催单</a>
									<a sale='<?=$fo['saleId']?>' class='pei btn btn-warning'<?php if (!$fo['pei'])
		      															echo "disabled";?> title='40分钟后订单未送达可以申请超值赔付,三个小时后我们就不处理了哦'>超时白吃</a>
		      					</span>
		      				</div>
		      				<div>
		      					<table class='black sale_table'>
		      						<tr>
		      							<td><strong>订单号：</strong><?=$fo['saleId']?></td>
		      							<td><strong>创建时间：</strong><?=$fo['createTime']?></td>
		      							<td><strong>商店电话：</strong><?=$fo['contact_phone']?></td>
		      						</tr>
		      						<tr>
		      							<td><strong>您的地址：</strong><?=$fo['user_addr']?></td>
				      					<td><strong>您的电话：</strong><?=$fo['user_l_tel']?></td>
				      					<td><strong>您的备注：</strong><?=$fo['taste']?></td>
		      						</tr>
		      					</table>
		      				</div>
		      				<?php
		      				if ($fo['validity']) { ?>
			      				<div class='d_store_comment'>
				      				<span class='pull-left deliver_title'>对外卖速度或商店的总体评价：</span>
				      				<?php
				      				if ($fo['score']) { ?>
				      					<div class='store_raty_dead store_score' data-score="<?=$fo['score']?>"></div>
				      					<div class='store_last_comment'><?=$fo['delivery_comment']?></div>
				      					
									<?php
									}
									else { ?>
										<div class="store_raty_alive store_score" sale="<?=$fo['saleId']?>"store="<?=$fo['storeId']?>"></div>
										<div class='store_comment hide'>
											<textarea placeholder='再说几句' row='10' col='10'></textarea>
											<div class='save_cancel'>
												<span class='black_a'>
													<a class='save' store='true'>保存</a>
												</span>
												<span class='black_a'>
													<a class='cancel' store='true'>取消</a>
												</span>
											</div>
										</div>
										<div class='store_last_comment hide'></div>
									<?php } ?>
									<span class='store_again'>
										<form action='/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>' method='post'>
				      						<input type='submit'value='再来易份' class='btn another_one'></input>
				      						<input type='hidden' name='food' value="<?php
				      							   foreach ($fo['food_one_sale'] as $key => $food) {
				      							   		if ($key == 0)
				      						       			echo $food['foodId'];
				      						       		else
				      						       			echo "-".$food['foodId'];
				      						   	   } ?>">
			      						</form>
			      					</span>
								</div>
							<?php } ?>
		      			</div>
		      			<div class='o_line_two'>
		      				<?php
		      				$sum = 0;
		      				foreach($fo['food_one_sale'] as $food) { ?>
		      					<div class='m_food_s'>
			      					<span class='m_food_n' title='<?=$food['foodName']?>（￥<?=$food['price']?>）x <?=$food['num']?>'>
			      						  <?=$food['foodName']?>（￥<?=$food['price']?>）x <?=$food['num']?>
			      					</span>
			      					<?php
			      					if ($fo['validity']) { 
				      					$sum += $food['price']*$food['num'];
				      					if ($food['taste_score']) { ?>
					      					<div class='food_raty_dead food_score' data-score="<?=$food['taste_score']?>"></div>
					      					<div class='food_last_comment'><?=$food['taste_comment']?></div>
										<?php
										}
										else { ?>
											<div class="food_raty_alive food_score" food="<?=$food['foodId']?>"sale="<?=$fo['saleId']?>"></div>
											<div class='food_comment hide'>
												<textarea placeholder='再说几句' row='10' col='10'></textarea>
												<div class='save_cancel'>
													<span class='black_a'>
														<a class='save'>保存</a>
													</span>
													<span class='black_a'>
														<a class='cancel'>取消</a>
													</span>
												</div>
											</div>
											<div class='food_last_comment hide'></div>
										<?php 
										} 
									} ?> 
									<span class='again_btn'>
										<form action='/store/<?=$fo['university_id']?>/<?=$fo['storeId']?>/#food-<?=$food['foodId']?>'method='post'>
				      						<input type='submit'value='再来易份' class='btn another_one'></input>
				      						<input type='hidden' name='food' value='<?=$food['foodId']?>'>
				      					</form>
			      					</span>
		      					</div>
		      				<?php
		      				} ?>
		      				<div class='m_food_total'>
		      					<span>总计：<?=$sum?></span>
		      				</div>
		      			</div>
		      		<?php } 
		      	} ?>
	    </div>
	</div>
	<div id="urgent_box" class="modal hide fade black" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>确认催单</h3>
		  </div>
		  <div class="modal-body">
		    <p>
		    	<span>你确认要提交订单号为<strong class='urgent_sale_id'></strong>的订单吗？</span>
		    </p>
		  </div>
		  <div class="modal-footer">
		     <a class="btn" data-dismiss="modal" aria-hidden="true">关闭</a>
		     <a class="btn btn-primary confirm_urgent">确认催单</a>
		  </div>
	</div>
	<div id="success_box" class="modal hide fade black" tabindex="-1" role="dialog" aria-hidden="true">
		  
		  <div class="modal-body">
		    <p class='urgent_info'>
		    	提交成功，我们的客服会尽快与商家联系，请您耐心等待，对您造成的不必要的麻烦，我们深感抱歉。
		    </p>
		  </div>
		  <div class="modal-footer">
		     <a class="btn" data-dismiss="modal" aria-hidden="true">关闭</a>
		  </div>
	</div>
	<div id="pei_box" class="modal hide fade black" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>确认提交超时白吃</h3>
		  </div>
		  <div class="modal-body">
		    <p>
		    	<span>你确认要提交订单号为<strong class='pei_sale_id'></strong>的订单吗？，我们的工作人员将尽快联系您！</span>
		    </p>
		  </div>
		  <div class="modal-footer">
		     <a class="btn" data-dismiss="modal" aria-hidden="true">关闭</a>
		     <a class="btn btn-primary confirm_pei">确认提交</a>
		  </div>
	</div>
<script type="text/javascript">
var star_path = "/img";
var store_hint_arr = ['太慢了','有点不耐烦','还好','比较快','飞一样'];
var food_hint_arr = ['太难吃','不喜欢','还过得去','好吃！','人间美味'];

$(document).ready(function(){
	$(document).click(function(e) {
        if (!$(e.target).is('.food_comment, .food_comment *'))
            $('.food_comment').addClass('hide');
        if (!$(e.target).is('.store_comment, .store_comment *'))
            $('.store_comment').addClass('hide');
    });
});
$('.urgent').click(function() {
	if ($(this).attr('disabled'))
		return;
	var sale_id = $(this).attr('sale');
	$('.urgent_sale_id').text(sale_id);
	$('.confirm_urgent').attr('sale', sale_id);
	$('#urgent_box').modal('show');
});

$('.pei').click(function() {
	if ($(this).attr('disabled'))
		return;
	var sale_id = $(this).attr('sale');
	$('.pei_sale_id').text(sale_id);
	$('.confirm_pei').attr('sale', sale_id);
	$('#pei_box').modal('show');
});

$('.confirm_urgent').click(function() {
	var sale_id = $(this).attr('sale');
	$.ajax({
		type: "get",
		url: "/management/urgent/"+sale_id,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        success: function(d){
			$('#urgent_box').modal('hide');
        	if (d.state == 1) {
        		$('.urgent_info').text('提交成功，我们的客服会尽快与商家联系，请您耐心等待，对您造成的不必要的麻烦，我们深感抱歉。');
        		$('.urgent').attr('disabled', true);
			}
			else if (d.state == 0)
        		$('.urgent_info').text('我们的客服已经和商家正在联系了，请您稍等');
        	else if (d.state == 2)
        		$('.urgent_info').text('提交失败，因为提交无效订单');
        	else if (d.state == 3)
        		$('.urgent_info').text('您已经提交催单，我们的客服正在处理，如仍需继续催单，请在十分钟后操作');
        	$('#success_box').modal('show');
        },
        error: function() {
        	alert('网络错误，请刷新后再试');
        }
	});
});

$('.confirm_pei').click(function() {
	var sale_id = $(this).attr('sale');
	$.ajax({
		type: "get",
		url: "/management/pei/"+sale_id,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        success: function(d){
			$('#pei_box').modal('hide');
        	if (d.state == 1) {
        		$('.urgent_info').text('提交成功，我们的客服会尽快确认订单详细信息，请您耐心等待，对您造成的不必要的麻烦，我们深感抱歉。');
        		$('.pei').attr('disabled', true);
			}
			else if (d.state == 0)
        		$('.urgent_info').text('我们的客服正在处理，请您稍等');
        	else if (d.state == 2)
        		$('.urgent_info').text('提交失败，因为提交无效订单');
        	else if (d.state == 3)
        		$('.urgent_info').text('您已经提交催单，我们的客服正在处理，如仍需继续催单，请在十分钟后操作');
        	$('#success_box').modal('show');
        },
        error: function(){
        	alert('网络错误，请刷新后再试');
        }
	});
});
$('.store_raty_dead').raty({
	path: star_path,
	readOnly:true,
	hints:store_hint_arr,
	score: function() {
		return $(this).attr('data-score');
	}
});
$('.store_raty_alive').raty({
	path: star_path,
	score: 3,
	hints:store_hint_arr
	
});
$('.food_raty_dead').raty({
	path: star_path,
	readOnly:true,
	hints:food_hint_arr,
	score: function() {
		return $(this).attr('data-score');
	}
});
$('.food_raty_alive').raty({
	path: star_path,
	score: 3,
	hints:food_hint_arr
	// click: function(e) {
	// }
});
$('.store_raty_alive').click(function(e) {
	if (!$(this).attr('dead'))
	{
	    $('.store_comment').addClass('hide');
		$(this).next().removeClass('hide');
	    e.stopPropagation();
	}
});
$('.food_raty_alive').click(function(e){
	if (!$(this).attr('dead'))
	{
		$('.food_comment').addClass('hide');
		$(this).next().removeClass('hide');
	    e.stopPropagation();
	}
});
function set_store_text(n,t,o) {
	n.removeClass('hide');
	n.text(t);
	o.parent().addClass('hide');
}
function send_store_text(st,sa,s,t) {
	$.ajax({
		url: '/management/store_comment/'+st+'/'+sa+'/'+s+'/'+t,
		type:'get',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: "json"
	});
}
function send_food_text(fo, sa, s,t) {
	$.ajax({
		url: '/management/food_comment/'+fo+'/'+sa+'/'+s+'/'+t,
		type:'get',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: "json"
	});
}
function store_text(th, i, j) {
	var o = $(th).parent().parent();
	var p = o.prev();
	var n = o.parent().next();
	var f= o.parent().prev();
	var s = f.raty('score');
	var t
	if (j) {
		t = store_hint_arr[s-1];
		if (i) {
			if ((t = p.val()) == "")
				t = store_hint_arr[s-1];
		}
	}
	else {
		t = food_hint_arr[s-1];
		if (i) {
			if ((t = p.val()) == "")
				t = food_hint_arr[s-1];
		}
	}
	set_store_text(n, t, o);
	var sa=f.attr('sale');
	if (j) {
		var st=f.attr('store');
		send_store_text(st,sa,s,t);
	}
	else {
		var fo=f.attr('food');
		send_food_text(fo,sa,s,t);
	}

	f.raty({
		path:star_path,
		readOnly:true, 
		score:s});
	f.attr('dead', true);
}
$('.save').click(function() {
	if ($(this).attr('store'))
		store_text(this, 1, 1);
	else
		store_text(this, 1, 0);
});

$('.cancel').click(function(){
	store_text(this, 0);
});
</script>
</div>
	    	