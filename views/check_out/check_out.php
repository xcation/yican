<div class="main">
	<div class="head_container blank">
<?php
if (isset($empty))
	echo "<div class='empty'><span class='rooter black'>对不住,您的框都是空的,点餐要一步一步来哦！</span></div>";
else { ?>
		<div class='c-cart'>
			<?php
			$count = 0;
			foreach ($my_cart as $store) { ?>
				<div class='c-one-store' store="<?=$count?>">
					<div class='c-store-name' store="<?=$count?>">
						<img class='c-store-logo'src="<?php
														if (@$store['imgLoc'])
															echo "/img/store/{$store['imgLoc']}";?>" />
						<span><?=$store['store_name']?></span>
					</div>
					<div class='c-blanket' store="<?=$count?>">
					</div>
					<?php
					if ( ((int)$store['delivery_type'] & 0x2) == 0) { ?>
						<div class='c-remark'>
							<div class='note-taste'>
								<a class='unclick'>不辣</a>
								<a class='unclick'>给点辣</a>
								<a class='unclick'>多点饭</a>
								<a class='unclick'>不要醋</a>
								<a class='unclick'>多点汤</a>
							</div>
							<span>备注：</span>
							<input class='taste-input'type="text"
							       store="<?=$store['store_id']?>" />
							<span>总价：￥<span class='c-cost' store="<?=$count?>"></span></span>
						</div>
					<?php
					}
					else { ?>
						<div class='c-remark'>
							<h6>这家店提供预定，即提供到店就餐的服务，请您在下面简要说明你们的到店
								<span class='order_option'>就餐时间</span>、
								<span class='order_option'>人数</span>、
								<span class='order_option'>口味</span>
								等等
							</h6>
							<input class='taste-input order_taste_input'type="text"
							       store="<?=$store['store_id']?>"placeholder="预定" maxlength='100'/>
						</div>
					<?php
					} ?>
				</div>
			<?php
				$count++;
			} ?>
		</div>
		<div class='c-user'>
			<script type="text/javascript">
				var has_default_address = 1;
			<?php
			if (count(@$all_address) == 0) { ?>
				has_default_address = 0;
			</script>
				<div class='text-error loc_error'>
					请输入送货地址和联系方式
				</div>
				<div class='c-location'>
					<span>地址：</span>
					<input type='text'style='width:250px'id="u_addr" value="<?=@$recent_loc['addr']?>" />
					<span>电话：</span>
					<input type='text'id="u_long_tel" value='<?=@$recent_loc['l_tel']?>' />
					<span>短号(可选)：</span>
					<input type='text'id="u_short_tel" value="<?=@$recent_loc['s_tel']?>" />
				</div>
			<?php
			}
			else { ?>
			</script>
			<div class='addr_list'>
			<?php
				$i = 0;
				foreach (@$all_address as $row) { ?>
				<div class='addr_item' pos="<?=$row['id']?>">
					<input type='radio' name='choosen_address' <?php if ($i++ == 0) echo "checked" ?> />
					<span class="addr_detail">
						地址：<span class="user_pos"><?=$row['userPos']?></span>;
						电话：<span class="main_phone"><?=$row['userPhone_main']?></span>;
						短号：<span class="short_phone"><?=$row['userPhone_short']?></span>
						<a class="blue modify_addr" pos="<?=$row['id']?>" />修改</a>&nbsp;<a class="blue delete_addr" pos="<?=$row['id']?>">删除</a>
					</span>
				</div>
			<?php
				} ?>
			</div>
			<?php
			}
			if (count($all_address) != 0) { ?>
				<div>
					<a class="btn btn-success" id="new_addr">新建一个地址</a>
				</div>
			<?php
			} ?>
			<!-- <div>
				<input type='radio' name='alipay' value='1'/>在线支付
				<input type='radio' name='alipay' value='0'/>现金付款
			</div> -->
			<div class='deliver-time'>
				<!-- 立马送出 -->
			</div>
			<div class='c-confirm-btn'>
				<span>总价：￥<span class='c-t-cost'></span></span>
				<?php echo $cap; ?>
				<div>
					请输入验证码后下单<input type='text' name='captcha' id='captcha'/>
					<button class='c-order-submit btn btn-warning'>
					</button>
				</div>
			</div>
		</div>
		<div id="input_code" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
			    <h3 class='black'>欢迎第一次使用一餐易餐</h3>
			</div>
			<div class="modal-body">
		    	<div class='black'>
		    		请在5分钟内输入验证码完成订单确认
		    		<span class='clock_tick red'>30</span>
		    	</div>
		    	<input type='text' name='confirm_code' id='confirm_code'/>
		    	<input type='hidden' name='phone' id='phone'>
		    	<span class='confirm_error red'></span>
		    	<input class='btn btn-warning' id='confirm_code_submit'value='确认'/>
			</div>
			<div class="modal-footer">
			</div>
		</div>
		<div id="c-location-change" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  		<div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			    <h3 id="h-order-confirm">修改您的地址</h3>
			    <h5 class="text-error hide" id="addr_err">不能为空，或输入有误</h5>
			</div>
	  		<div class="modal-body d-c-location">
	  			<input type='hidden' id="modi_pos"/>
			    <input type="text" id="addr"placeholder="您的地址"/>
			    <input type="text" id="long_tel"placeholder="联系电话(11位长号)"/>
			    <input type="text" id="short_tel"placeholder="短号(可选)"/>
			</div>
	  		<div class="modal-footer">
	    		<button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
	    		<button class="btn btn-primary" id="save_addr" op='0'>保存</button>
	  		</div>
		</div>
<script type="text/javascript">
$('#new_addr').click(function() {
	$('#h-order-confirm').text('创建一个新地址');
	$('#c-location-change').modal('show');
	$('#save_addr').attr('op', '0');
	$("#long_tel").val('');
	$("#short_tel").val('');
	$("#addr").val('');
});
$('.delete_addr').click(function() {
	var pos = $(this).attr('pos');
	var obj = $(this);
	$.ajax({
		type: "POST",
		url: "/check_out/delete_addr",
		data:{delete_pos: pos},
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        success: function(d){
        	if (d.state != 1)
        		alert("删除失败");
        	else
        		obj.parent().parent().detach();
        },
        error: function(){
        	alert('网络错误');
        }
	});
})
$("#save_addr").click(function() {
	var addr = $("#addr").val();
	var l_t = $("#long_tel").val();
	var s_t = $("#short_tel").val();
	var pos = $('#modi_pos').val();
	if (addr == "" || l_t == "" || l_t.length != 11 || isNaN(l_t) || (s_t != "" && s_t.length != 6)) {
		$("#addr_err").removeClass('hide');
	}
	else {
		$("#addr_err").addClass('hide');

		$.cookie('addr', addr, {path: '/', expire: 30});
		$.cookie('l_tel', l_t, {path: '/', expire: 30});
		if (s_t != "")
			$.cookie('s_tel', s_t, {path: '/', expire: 30});
		op = $(this).attr('op');
		if (op == '1') {
			$.ajax({
				type: "POST",
				url: "/check_out/modify_addr",
				data:{addr:addr, main_phone: l_t, short_phone:s_t, modify_pos: pos},
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
		        dataType: "json",
		        success: function(d){
		        	if (d.state != 1) {
		        		alert("提交失败");
		        	}
		        	else {
						var obj = $('.addr_item[pos='+pos+']');
						obj.find('.user_pos').first().text(addr);
						obj.find('.main_phone').first().text(l_t);
						obj.find('.short_phone').first().text(s_t);
		        	}
		        },
		        error: function(){
		        	alert('网络错误');
		        }
			});
		}
		else if (op == '0') {

			$.ajax({
				type: "POST",
				url: "/check_out/new_addr",
				data:{addr:addr, main_phone: l_t, short_phone:s_t},
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
		        dataType: "json",
		        success: function(d){
		        	if (d.state != 1) {
		        		alert("提交失败");
		        	}
		        	else {
						html = "<div class='addr_item' pos='"+d.pos+"'>"+
							   		"<input type='radio' name='choosen_address' checked />"+
									"<span class='addr_detail'>"+
										"地址：<span class='user_pos'>"+addr+"</span>;"+
										"电话：<span class='main_phone'>"+l_t+"</span>;"+
										"短号：<span class='short_phone'>"+s_t+"</span>"+
									"</span>"+
								"</div>";
						$('.addr_list').append(html);
		        	}
		        },
		        error: function(){
		        	alert('网络错误');
		        }
			});
		}
	    $('#c-location-change').modal('hide');
	}
});
$('.modify_addr').click(function() {
	p = $(this).parent();
	$("#modi_pos").val($(this).attr('pos'));
	$("#addr").val(p.children('.user_pos').text());
	$("#long_tel").val(p.children('.main_phone').text());
	$("#short_tel").val(p.children('.short_phone').text());
	$('#save_addr').attr('op', '1');
	$('#c-location-change').modal('show');
});
$('#confirm_code_submit').click(function() {
	var confirm_code = $('#confirm_code').val();
	var phone = $('#phone').val();
	var ali = $(':radio[name=alipay][checked]').val();
	$.ajax({
		type: "POST",
		url: "/check_out/confirm_phone",
		data:{confirm_code:confirm_code, phone: phone, cart:cart, loc:loc, ali:ali},
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        beforeSend:function(){
        	$('#confirm_code_submit').val('正在确认...');
        	$('#confirm_code_submit').attr('disabled', true);
        },
        success: function(d){
        	if (d.phone == 1) {
        		if (d.state == 1) {
            		$.cookie('my_cart', null);
            		if (d.alipay_url) {
            			alert('确定提交在线支付订单');
            			window.location.href=d.alipay_url;
					}
					else
						window.location.href='/management/success';
				}
				else if (d.state == 0) {
					window.location.href='/management/error/'+d.error;
				}
			}
			else if (d.phone == 0) {
				$('.confirm_error').text('验证码错误');
				$('#confirm_code_submit').val('确认');
        		$('#confirm_code_submit').removeAttr('disabled');
			}
        },
        error: function(){
        	alert('网络错误');
        }
	});
});
function adder(a, b) {
	return (parseFloat(a) + parseFloat(b)).toFixed(1);
}
function get_rel(obj) {
	var rel = $(obj).attr("rel");
	rel = rel.split('-');
	return rel;
}
function clear_blanket(rel) {
	cart[rel[0]]["blanket"].splice(rel[1], 1);
	if (cart[rel[0]]["blanket"].length == 0) {
		cart.splice(rel[0], 1);
		$(".c-one-store[store='"+rel[0]+"']").detach();
	}
}
function clear_food(obj, rel) {
	var obj_1 = cart[rel[0]]["blanket"][rel[1]]["food"];
	obj_1.splice(rel[2], 1);
	if (obj_1.length == 0)
		clear_blanket(rel);
}
var cart = <?=$s_my_cart?>;
var loc;
	// cart = $.parseJSON(cart);
var valid;
function show_cart() {
	valid = 1;
	var t_cost = 0;
	for (var store_num in cart) {
		var c_b = "<table class='check-basket'> \
					<thead class='c-h-title'> \
					  <tr> \
						<th class='h-c-blanket-num'>美食框</th> \
						<th class='h-c-food-name'> \
							<span class='h-c c-food-name'>食物</span> \
							<span class='h-c c-price'>单价</span> \
							<span class='h-c c-num'>数量</span> \
							<span class='h-c c-total'>总价</span> \
						</th> \
					  </tr> \
					</thead> \
					<tbody>";
		var blanket = cart[store_num]['blanket'];
		var food_cost;
		var store_cost = 0;
		for (var blanket_num in blanket) {
			if (blanket[blanket_num]['food'].length == 0)
				continue;
			c_b += "<tr> \
						<td class='c-dish c-blanket-num'>第"+(parseInt(blanket_num)+1)+"框</td> \
						<td class='c-right'> \
							<table class='t-food-in-basket'> \
								<tbody>";
			var food = blanket[blanket_num]['food'];

			for (var food_num in food) {
				food_cost = (food[food_num].price * food[food_num].number).toFixed(1);
				store_cost = adder(food_cost, store_cost);
				c_b +=  "<tr class='c-right-in'> \
							<td class='c-dish c-food-name'>"+food[food_num].food_name+"</td> \
							<td class='c-dish c-price'>￥"+food[food_num].price+"</td> \
							<td class='c-dish c-num'> \
							  <div class='c-num-content'> \
			             		<a class='cart-widget mar_10 minus' rel='"+store_num+'-'+blanket_num+'-'+food_num+
			             		"'></a> \
			             		<input type='text'class='food_quantity-input' value='"+
			             			food[food_num]["number"]+
			             		"'></input>"+
				             	"<a class='cart-widget adder mar_10' rel='"+store_num+'-'+blanket_num+'-'+food_num+
				             	"'></a> \
				              </div> \
				            </td> \
							<td class='c-dish c-total'>￥"+food_cost+
							"</td>";
			}
			c_b += 				"</tbody> \
							</table> \
						</td> \
					</tr>";

		}
		c_b += "</tbody> \
		      </table>";
		$(".c-cost[store='"+store_num+"']").text(store_cost);
		if (parseFloat(store_cost) < parseFloat(cart[store_num].delivery_cost)) {
			valid = 0;
			$(".c-store-name[store='"+store_num+"']").append("<span class='warning'> \
									   	未满起送价"+cart[store_num].delivery_cost+
										"元。<a href='/store/"+cart[store_num].university_id+'/'+cart[store_num].store_id+
										"'>再去看看</a> \
									  </span>");
		}
		else {
			$(".c-store-name[store='"+store_num+"']").children('span.warning').detach();
		}
		$(".c-blanket[store='"+store_num+"']").html(c_b);
		t_cost = adder(t_cost, store_cost);
	}
	$(".c-t-cost").text(t_cost);
	$('.adder').click(function() {
		var rel = get_rel(this);
		var quantity = $(this).parent().find('.food_quantity-input').val();
		cart[rel[0]]["blanket"][rel[1]]["food"][rel[2]]["number"] = ++quantity;
		show_cart();
		// var amount = $(this).parent().prev().text() * quantity;
		// $(this).parent().next().text(amount.toFixed(1));
		// $(this).prev().attr('value', quantity);

	});
	$('.minus').click(function() {
		var rel = get_rel(this);
		var quantity = $(this).next().attr('value');
		if (quantity == 1) {
			clear_food(this, rel);
		}
		else {
			cart[rel[0]]["blanket"][rel[1]]["food"][rel[2]]["number"] = --quantity;
		}
		show_cart();
	});

	var o = $('.c-order-submit');
	if (valid) {
		o.removeClass('c-disabled');
		o.attr("disabled",false);
		o.text("确认下单");
	}
	else {
		o.addClass('c-disabled');
		o.attr("disabled",true);
		$('.c-order-submit').text("暂时不能提交");
	}

	$.cookie('my_cart', JSON.stringify(cart), {path: '/', expire: 30});
}
function make_button_avai() {
	$('.c-order-submit').text('确认下单');
    $('.c-order-submit').removeAttr('disabled');
}
var second;
var time_tick;
var phone;
function remainTime() {
	if (second > 0) {
		time_tick = setTimeout('remainTime()', 1000);
		second--;
	}
	else {
		clearTimeout(time_tick);
		second = '时间到，请重新发起订单';
		$('#confirm_code').attr('disabled', 'disabled');
		$('.modal-footer').append('<button class="btn" data-dismiss="modal" aria-hidden="true">返回</button>');
		make_button_avai();
       	$('#confirm_code_submit').attr('disabled', true);

	}
	$('.clock_tick').text(second);
}
$('.note-taste').children('a.unclick').click(function(){
		var o = $(this).parent().next().next();//();
		var s= o.attr('value') + ' ' + $(this).text();
		o.attr('value', s);
		$(this).removeClass('unclick');
		$(this).empty();
	});
$('.c-order-submit').click(function() {
	var count = 0;
	var pos;
	$("input.taste-input").each(function() {
		cart[count]['taste'] = $(this).val();
		count++;
	});
	loc = {};
	if (has_default_address == 0) {
		loc.addr = $('#u_addr').val();
		loc.l_tel = $('#u_long_tel').val();
		loc.s_tel = $('#u_short_tel').val();
	}
	else {
		var addr = $(':radio[name=choosen_address][checked]');
		if (addr.length == 0) {
			alert('请选择地址');
			return;
		}
		pos = addr.parent().attr('pos');
		addr = addr.next();
		loc.addr = addr.children('.user_pos').first().text();
		loc.l_tel = addr.children('.main_phone').first().text();
		loc.s_tel = addr.children('.short_phone').first().text();
	}
	if (loc.addr == "" || loc.l_tel == "") {
		$('.loc_error').text('地址或长号不能为空');
		$('.loc_error').css('display', 'block');
		return;
	}
	else if (loc.l_tel.length != 11) {
		$('.loc_error').text('请输入11位长号');
		$('.loc_error').css('display', 'block');
		return;
	}
	else if (isNaN(loc.l_tel)) {
		$('.loc_error').text('长号输入有误');
		$('.loc_error').css('display', 'block');
		return;
	}
	show_cart();
	if (valid) {
		$('.loc_error').css('display', 'none');
		phone = loc.l_tel;
		var captcha = $('#captcha').val();
		$.cookie('addr', loc.addr, {path: '/', expire: 30});
		$.cookie('l_tel', loc.l_tel, {path: '/', expire: 30});
		$.cookie('s_tel', loc.s_tel, {path: '/', expire: 30});

		var ali = $(':radio[name=alipay][checked]').val();
		$.ajax({
			type: "POST",
			url: "/check_out/up_order",
			data:{cart: cart, loc: loc, captcha: captcha, has_default_address: has_default_address, pos: pos, ali: ali},
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
            dataType: "json",
            beforeSend:function(){
            	$('.c-order-submit').text('正在发送...');
            	$('.c-order-submit').attr('disabled', true);
            },
            success: function(d){
            	if (d.state == 1) {
            		$.cookie('my_cart', null);
            		if (d.alipay_url) {
            			alert('确定提交在线支付订单');
            			window.location.href=d.alipay_url;
					}
					else
						window.location.href='/management/success';
				}
				else if (d.state == 0) {
					window.location.href='/management/error/'+d.error;
				}
				else if (d.state == 2) {
					alert(d.error);
					make_button_avai();
				}
				else if (d.state == 3) {
					$('#input_code').modal({
						backdrop: false
					});
					$('#input_code').modal('show');

					$('#phone').val(phone);
					$('#confirm_code').val('');
					$('#confirm_code').removeAttr('disabled');
					$('#confirm_code_submit').removeAttr('disabled');
					$('.confirm_error').text('');
					$('.modal-footer').html('');
					second = 300;
					remainTime();
				}
				else if (d.state == 4) {
					alert('您输入的验证码有误');
					location.reload(true);
				}
            },
            error: function(){
            	alert("出现网络错误");
            }
		});
	}
});
show_cart();
</script>
<?php
	} ?>
	</div>
</div>