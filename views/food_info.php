<?php
// 说明：获取完整URL

function curPageURL() 
{
    $pageURL = 'http';
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
function write_state($state) {
	if (!$state)
		echo "休息中";
	else
		echo "来易份";
}
?>



<div class="main">
	<div class="head_container food">
		<div class="gonggao">
			<div>公告：<?=$gonggao?></div>
			<div>店铺地址：<?=$location?></div>
			<div>店铺简介：<?=$briefIntroduction?></div>
			<div>营业时间：<?=$yinyeshijian?></div>
			<div>起送价：<?=$qisongjia?></div>
		</div>

		<div class='all_food'>
				<ul class="my_nav nav nav-list bs-docs-sidenav affix" data-spy="affix" 
					data-offset-top="50">
				<?php
					foreach($food_type as $val) { ?>
						<li>
							<a title="<?=$val['foodTypeName']?>"class='food_info_type_title'href="#cata-<?=$val['foodTypeId']?>">
								<i class="icon-chevron-right"></i>
								<?=$val['foodTypeName']?>
							</a>
						</li>
				<?php } ?>
		        </ul>
	       	<div class="food_info">
			<?php
			foreach($food_type as $val) { ?>
	        	<div class='section'id='cata-<?=$val['foodTypeId']?>'>
					<div class="food_type">
						<?=$val['foodTypeName']?>
					</div>
					<div class="my_food">
						<?php
							$my_food = $food_info[$val['foodTypeId']];
							if (count($my_food['with_img']) > 0) { ?>
								<table class="food_table">
									<tbody>
								<?php
									$count = 0;
									$row = $my_food['with_img'];
									foreach ($row as $val_2) {
										if ($count % 3 == 0) 
											echo "<tr>";
										?>
										<td id='food-<?=$val_2['foodId']?>'class="<?php if ($count % 3 != 2) echo "pic_food";
																						else echo"pic_food_last";
																						?> one-food-<?=$val_2['foodName']?>" 
																		   title="<?=$val_2['note']?>">
											<div class="<?php if ($count % 3 != 2) echo "pic_food_not_last";?>">
												<div class="one-food-lineone"><img class="food-img" src="/img/food/<?=$val_2['imgLoc']?>"></div>
												<div class="one-food-linetwo">
													<div>
														<span><?=$val_2['foodName']?></span>
														<span class='s_f_price'>￥<?=$val_2['price']?></span>
													</div>
													<div>
														<span class='f_note'><?=$val_2['note']?></span>
													</div>
													<div>
														<span>月销：<?=@$val_2['total_buyer_month']?></span>
													</div>
													<div class="f_food_score f_food_score_yes" data-score='<?=@$val_2['avg_score_month']?>'>
													</div>
													<div class='f_food_yf'>
														<a <?php if (!$state) echo "disabled"?>
															class='btn btn-success img-order-one order-one'food-id='<?=$val_2['foodId']?>'
															food-name='<?=$val_2['foodName']?>'
															food-price='<?=$val_2['price']?>'>
															<?php write_state($state); ?>
														</a>
													</div>
												</div>
											</div>
										</td>
										<?php
										if ($count % 3 == 2)
											echo "</tr>";
										$count++;
									}
								?>
									</tbody>
								</table>
							<?php } 
							if (count($my_food['no_img']) > 0) { 
								foreach ($my_food['no_img'] as $v) { ?>
									<div class='food_no_img' id='food-<?=$v['foodId']?>'>
										<span class='no_f_name'><?=$v['foodName']?></span>
										<span class='no_f_price'>￥<?=$v['price']?></span>
										<span class='no_f_num'>月销：<?=@$v['total_buyer_month']?></span>

										<div class="f_food_score f_food_score_no"  data-score='<?=$v['avg_score']?>'></div>
										<span class='no_f_btn'>
											<a <?php if (!$state) echo "disabled"?>
												class='btn btn-success food_no_img_line order-one'food-id='<?=$v['foodId']?>'
												food-name='<?=$v['foodName']?>'
												food-price='<?=$v['price']?>'>
												<?php write_state($state); ?>
											</a>
										</span>
										<div>
											<span class='f_note'><?=$v['note']?></span>
										</div>
									</div>

								<?php
								}
							}
							?>
					</div>
				</div>

			<?php } ?>
			</div>
		<script type="text/javascript">
		$('.affix-top').affix();

		</script>
	    </div>
	</div>
</div>
<div class="cart">
	<div class="cart-info">
		<button class='btn add-a-blanket'>加一框</button>
		<button class='btn delete-a-store'>删除当前店</button>
		<span class="cart-store">
		</span>
		<span class='total-cost'>
			总计：
			<span class='ex-total-cost'></span>
		</span>
		<span class='ex-delivery'></span>
		<span class='cart_checkout'>
			<button class="btn btn-warning" id="check_out">买单</button>
		</span>
	</div>
</div>

<div id="check-out-confirm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="h-order-confirm">订单确认检查</h3>
  </div>
  <div class="modal-body">
    <p>对不起，您还有<span class='ex-d-c'></span>家店未达到起送价，他们分别是</p>
    <div class='ex-s-n'>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <!-- <button class="btn btn-primary">确认提交</button> -->
  </div>
</div>
<form style='display: none;'method='post' action='/check_out' id='f-my-cart'>
	<input name='my_cart' type='hidden' value=''></input>
</form>
<?php
	if (@$another_one) { ?>
		<script type="text/javascript">
			$(document).ready(function() {
				var another_food = '<?=$another_one?>';
				var food_arr = another_food.split('-');
				for (var i in food_arr) {
					$("a[food-id="+food_arr[i]+"]").trigger('click');
				}
			});
		</script>
	<?php
}
?>
<script type="text/javascript">
	var this_store_id=<?=$store_id?>;
	var this_store_name='<?=$store_name?>';
	var this_store_delivery_cost="<?=$delivery_cost?>";
	var this_store_university_id='<?=$university_id?>';
	// $.cookie('my_cart', '');
	// var cart = $.cookie('my_cart');
	// cart = JSON.parse(cart);
	var cart =get_cart();
var open_store=-1;
var start = 1;
var active_store = cart.length - 1;
var active_blanket = -1;
var active_blanket_store = -1;
var star_path = '/img';
if (active_store > -1) {
	for (var i in cart) {
		if (cart[i].store_id == this_store_id) {
			active_blanket_store = active_store = i;
			break;
		}
	}
	if (active_blanket_store > -1)
		active_blanket = cart[active_blanket_store]["blanket"].length - 1;
}
$('.f_food_score').raty({
	path: star_path,
	readOnly:true,
	score: function() {
		return $(this).attr('data-score');
	}
});

$('.add-a-blanket').click(function() {
	if (active_blanket_store >= 0) {
		var len = cart[active_blanket_store]["blanket"].length;
		if (cart[active_blanket_store]['blanket'][len - 1]['food'].length > 0) {
			cart[active_blanket_store]['blanket'][len]={"blanket":1,"open": 1,"food":[]};
			++active_blanket;
		}
		show_cart();
	}
});

function check_em(obj) {
	var em=1;
	for (var i=obj.length-1;i>=0;i--)
		if (typeof obj[i] != 'undefined') {
			em=0;
			break;
		}
	return em;
}
function clear_store(rel) {
	cart.splice(rel, 1);
	if (rel == active_blanket_store) {
		active_blanket_store = -1;
		active_blanket = -1;
	}
	if (cart.length > 0) 
		active_store = 0;
	else 
		active_store = -1;
}
function clear_blanket(rel) {
	// delete cart[rel[0]]["blanket"][rel[1]];
	cart[rel[0]]["blanket"].splice(rel[1], 1);

	if (cart[rel[0]]["blanket"].length == 0) {
		clear_store(rel[0]);
	}
	else {
		if (parseInt(rel[1], 10) == active_blanket)
			active_blanket = cart[rel[0]]["blanket"].length - 1;
		else
			active_blanket--;
	}
}
function clear_food(obj, rel) {
	var obj_1 = cart[rel[0]]["blanket"][rel[1]]["food"];
	// delete obj_1[rel[2]];//.splice(rel[2], 1);
	obj_1.splice(rel[2], 1);
	// $(obj).parent().parent().detach();

	if (obj_1.length == 0)
		clear_blanket(rel);
	
	// var l = obj_1.length;
}
function get_rel(obj) {
	var rel = $(obj).attr("rel");
	rel = rel.split('-');
	return rel;
}
function get_basket_total(store_num, blanket_num) {
	var obj = cart[store_num]['blanket'][blanket_num]['food'];
	var sum = 0;
	for (var i in obj) 
		sum += obj[i]['price'] * obj[i]['number']
	return sum.toFixed(2);
}
function get_store_total(store_num) {
	var obj = cart[store_num].blanket;
	var sum = 0;
	for (var i = 0; i < obj.length; i++)
		sum = parseFloat(sum) + parseFloat(get_basket_total(store_num, i));
	return parseFloat(sum).toFixed(2);	
}
var ex_delivery;
var all_total_cost;
var not_deliver_store_num;
function show_cart() {
	if (!start) {
		$(".store_name").detach();
		$('.food-in-cart').detach();
	}
	else
		start = 0;
	$('.cart-store').prepend("<span class='store_name'></span>");
	$(".cart").prepend('<div class="food-in-cart"></div>');

	var h_store_name = "";
	all_total_cost = 0;
	ex_delivery = 0;
	not_deliver_store_num = new Array();
	for (var store_num in cart) {
		h_store_name += "<button class='btn b_store_name";
		if (active_store == store_num)
			h_store_name += " btn-danger";
		h_store_name += "' store_id='"+cart[store_num]["store_id"]+"'>";
		h_store_name += cart[store_num]["store_name"] + "</button>";
		var blanket = cart[store_num]["blanket"];
		var h_blanket = "<div class='blanket";
		if (cart[store_num]['store_id'] != open_store)
			h_blanket += " hide";
		h_blanket += "' store_id='"+cart[store_num]["store_id"]+"'> \
					<div class='cart-left'>";
		for (var blanket_num in blanket) {
			var k = parseInt(blanket_num, 10) + 1;
			h_blanket += "<div id='b-"+store_num+'-'+blanket_num+"'> \
						 <div class='blanket-line-one'> \
						 <a class='blanket-state";
			if (blanket[blanket_num]['open']) 
				h_blanket += " b-open";

			h_blanket += "'></a> \
						 <div class='num-blanket'> \
						 	第"+k+"框 \
						 <div class='blanket-total-cost'><span>这一框共:"+get_basket_total(store_num, blanket_num)+
						 	"元</span> \
						 	</div>";
			if (active_blanket_store == store_num)
				h_blanket += "<input type='radio' style='margin-top:-4px;'title='激活当前框' class='active_blanket' rel='"+
								store_num+'-'+blanket_num+
								"' name='active_blanket'";
			if (active_blanket_store == store_num && active_blanket == blanket_num)
				h_blanket += "checked='checked'";
			if (active_blanket_store == store_num)
				h_blanket += "></input>";
			if (active_blanket_store == store_num && active_blanket == blanket_num)
				h_blanket += "<div class='note-active' title='当前选菜框'> \
						 		<b class='star'></b> \
						 		<b class='star'></b> \
						 		<b class='star'></b> \
							  </div>";
		 	h_blanket += "</div> \
						  </div> \
						  </div> \
						  <div> \
						  <table class='t-blanket'> \
							<thead> \
								<tr> \
									<th class='food_name'>食物</th> \
									<th class='food_price'>单价</th> \
									<th class='food_quantity'>数量</th> \
									<th class='food_total_cost'>总价</th> \
									<th><a class='delete-one-line' rel='"+store_num+'-'+blanket_num+"'> \
										清空这一框</a> \
									</th> \
								</tr> \
							</thead> \
							<tbody>";
			var food_in_blanket = blanket[blanket_num]["food"];
			for (var food_id in food_in_blanket) {
				h_blanket += "<tr class='food_line'> \
								<td class='food_name'>"+
									food_in_blanket[food_id]["food_name"]+
								"</td> \
								<td class='food_price'>"+
				             		food_in_blanket[food_id]["price"]+
				             	"</td> \
				             	<td class='food_quantity'> \
				             		<a class='cart-widget minus mar_10' rel='"+store_num+'-'+blanket_num+'-'+food_id+"'></a> \
				             		<input class='food_quantity-input' type='text'value='"+
				             			food_in_blanket[food_id]["number"]+
				             		"'></input>"+
				             	"<a class='cart-widget adder mar_10' rel='"+store_num+'-'+blanket_num+'-'+food_id+"'></a></td> \
				             	<td class='food_total_cost'>"+
				             		(food_in_blanket[food_id]["price"]*food_in_blanket[food_id]["number"]).toFixed(1)+
				             	"</td> \
				             	<td> \
				             		<a class='cart-widget delete-food' rel='"+store_num+'-'+blanket_num+'-'+food_id+"'> \
				             		</a> \
				             </tr>";
			}
			h_blanket += "		</tbody> \
						</table> \
					\
					</div>";
		}
		var store_total=get_store_total(store_num);
		all_total_cost = (parseFloat(all_total_cost)+parseFloat(store_total)).toFixed(2);
		var delivery_cost=cart[store_num].delivery_cost;
		h_blanket += "</div> \
					  	<div class='cart-right'> \
					  		<span>您在我们家——"+
					  			  cart[store_num].store_name+
					  			  "总共花了"+
					  			  store_total+
					  		"元</span> \
					  		 <div> \
					  		 	我们家的起步价是"+
					  		 	cart[store_num].delivery_cost+"元,";
					if (parseFloat(store_total) < parseFloat(delivery_cost)) {
						h_blanket += "还差<span class='not-deliveryed'>"+(parseFloat(delivery_cost) - parseFloat(store_total)).toFixed(2)+"元</span>";
						not_deliver_store_num.push(cart[store_num].store_name);
						ex_delivery += 1;
					}
					h_blanket += "可以起送 \
					         </div> \
					  		 <div><a style='color:rgb(212, 90, 90);;'href='/store/"+
					  		 	cart[store_num].university_id+'/'+
					  		 	cart[store_num].store_id+"'>再去我们家看看</a></div> \
					  	</div> \
					  </div>";
		$('.food-in-cart').append(h_blanket);

		// $('.food-in-cart').append("</div>");	
	}
	$('.ex-total-cost').text(all_total_cost+'元');
	if (all_total_cost > 0 && ex_delivery == 0)
		$('.ex-delivery').text('全部可以起送');
	else 
		$('.ex-delivery').text('还有'+ex_delivery+'家店未满起送价');
	$('.store_name').append(h_store_name);

	$("button.b_store_name").click(function() {
		var m_s_i = $(this).attr('store_id');
		open_store = m_s_i;
		var count = 0;
		if ($(this).index() != active_store) {
			$(this).addClass('btn-danger');

			$("button.b_store_name").each(function() {
				if (count++ == active_store)
					$(this).removeClass('btn-danger');
			});
			active_store = $(this).index();
			// active_blanket = cart[active_store]['blanket'].length - 1;
			show_cart();
		}
		else {
			$(".blanket").each(function() {
			 	if ($(this).attr('store_id') == m_s_i) {
			 		$(this).toggleClass('hide');
			 	}
			 	else
			 		$(this).addClass('hide');
			 	
			});
		}
		
	});
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
			// var amount = $(this).parent().prev().text() * quantity;
			// $(this).parent().next().text(amount.toFixed(1));
			// $(this).next().attr('value', quantity);
		}
		show_cart();

	});	
	$(".delete-one-line").click(function() {
		clear_blanket(get_rel(this));
		show_cart();

	});
	$(".delete-food").click(function() {
		clear_food(this, get_rel(this));
		show_cart();
	});
	$('.blanket-state').click(function() {
		$(this).toggleClass('b-open');
		$(this).parent().parent().next().children().toggleClass('hide');
	});
	$(".active_blanket").click(function() {
		var rel = get_rel(this);
		active_blanket_store = rel[0];
		active_blanket = rel[1];
		show_cart();
	});
	$(document).click(function(e){
		if (!$(e.target).is('.blanket, .blanket *') && 
			!$(e.target).is('.cart-info, .cart-info *') &&
			!$(e.target).is('.order-one')) {
        	$('.blanket').addClass('hide');
        }
	});
	$.cookie('my_cart', JSON.stringify(cart), {path: '/', expire: 30});
}
show_cart();


function order_one(obj, i, len, food_id) {
	var obj_1 = cart[i]['blanket'][len]['food'];
	for (var i in obj_1) {
		if (obj_1[i]['food_id'] == $(obj).attr('food-id')) {
			obj_1[i]['number'] += 1;
			return;
		}
	}
	obj_1.push(
	{"food_id": $(obj).attr('food-id'),
	 "food_name": $(obj).attr('food-name'),
	 "price": $(obj).attr('food-price'),
	 "number": 1});
}
$(".order-one").click(function() {
	if (typeof $(this).attr('disabled') != 'undefined')
		return;
	// var my_cart = $.cookie('my_cart');
	// my_cart = $.parseJSON(my_cart);
	if (active_blanket_store > -1) {
		if (active_blanket > -1)
			order_one(this, active_blanket_store, active_blanket);
		else {
			var len = cart[active_blanket_store]['blanket'].length - 1;
			order_one(this, active_blanket_store, len);
		}
	}
	else {
		cart.push(
			{
				"store_id": this_store_id,
				"university_id": this_store_university_id,
				"store_name": this_store_name,
				"delivery_cost":this_store_delivery_cost,
				"blanket":
					[
						{
							"blanket_num":1,
							"open":1,
							"food":[]
						}
					]
			});

		active_store = active_blanket_store = cart.length - 1;
		open_store = cart[active_store].store_id;
		active_blanket = 0;
		order_one(this, active_blanket_store, 0);
	}
	show_cart();
});
$('.delete-a-store').click(function() {
	clear_store(active_store);
	show_cart();
});
$('#check_out').click(function() {
	if (all_total_cost > 0 && ex_delivery == 0) {
		$("input[name='my_cart']").attr('value', JSON.stringify(cart));
		$('#f-my-cart').submit();
	}
	else {
		if (all_total_cost == '0') 
			$('.modal-body').html("对不起，你的所有框暂时都是空的，继续看看吧");
		else {
			var s_n = "<ul>";
			for (var i in not_deliver_store_num)
				s_n += "<li>"+not_deliver_store_num[i]+"</li>";
			s_n += "</ul>";
			$('.modal-body').html("<p>对不起，您还有 \
									<span class='ex-d-c'>"+ex_delivery+
									"</span>家店未达到起送价，他们分别是 \
								   </p> \
								   <div class='ex-s-n'>"+s_n+
    							   "</div>");
		}
			$('#check-out-confirm').modal('show');
	}
});
</script>
