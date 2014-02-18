<div class='s_right'>
	<?php
		foreach($data as $food_type) { ?>
			<div class='s_one_food_type accordion-group'>
				<div class='s_food_type_name accordion-heading black_a'>
					<a class='food_type_title' title='<?=$food_type['food_type_name']?>'data-toggle='collapse'data-parent=".s_right"data-target="#s_food_in_<?=$food_type['food_type_id']?>">
						<?=$food_type['food_type_name']?>
						<i></i>
					</a>
					<a class='delete_food_type' food-type='<?=$food_type['food_type_id']?>'>删除这一种类</a>
				</div>
				<div class='s_food_in accordion-body collapse' id="s_food_in_<?=$food_type['food_type_id']?>">
					<?php
					$count = count($food_type['food_in']);
					foreach($food_type['food_in'] as $food) { ?>
						<div class='s_food'>
							<div class='s_food_left' food="<?=$food['foodId']?>">
								<div>
									<form name="form" action="/shanghu/img_upload/<?=$store_id?>" id="<?=$food['foodId']?>"method="post" enctype="multipart/form-data">
										<img class='s_food_img'src="<?php
																		if (@$food['imgLoc'])
																			echo "/img/food/{$food['imgLoc']}";?>">
										<input id="img_upload_<?=$food['foodId']?>"name="img_upload_<?=$food['foodId']?>" style='width:200px' type='file' size='100' />
										<input type='button' class='img_upload_btn' id="<?=$food['foodId']?>"value='修改图片'/>
									</form>
								</div>
								<div>
									<div>
										菜名<input op='0'food="<?=$food['foodId']?>"class='s_i s_i_food_name'type="text" value="<?=$food['foodName']?>">
									</div>
									<div>
										价格<input op='1'food="<?=$food['foodId']?>"class='s_i s_i_price'type="text" value="<?=$food['price']?>">
									</div>
									<div>
										备注<input op='2'food="<?=$food['foodId']?>"class='s_i s_i_note' type="text" value="<?=$food['note']?>">
									</div>
								</div>
								<div class='waiting_upload'>
									<span class='s_a_state' food="<?=$food['foodId']?>">
									</span>
								</div>
							</div>
							<div class='s_food_right'>
								<div class='black_a'>
									<a class='s_delete_one' food="<?=$food['foodId']?>">x</a>
								</div>
								<div>
									<input type='radio' value='1'class='avail'name="<?=$food['foodId']?>" 
									 <?php
									 		 food_avai($food); ?> >
									<label>有</label>
								</div>
								<div>
									<input type='radio' value='0'class='avail'name="<?=$food['foodId']?>"
									 <?php
									 		echo not_food_avai($food); ?> >
									<label>无</label>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class='s_food'>
						<div class='s_add_one black_a'>
							<a class='a_s_add_one' title='加菜' value='<?=$food_type['food_type_id']?>'>+</a>
						</div>
					</div>
				</div>
			</div>	
	<?php } ?>
	<div class='s_one_food_type accordion-group'>
		<div class='s_food_type_name accordion-heading black_a'>
			<a class='accordion-toggle'data-toggle='collapse'data-parent=".s_right"data-target="#new_type">
				增加种类
				<i></i>
			</a>
		</div>
		<div id="new_type"class='s_food_in accordion-body collapse'>
			<div>
				<label>请输入种类名称</label>
				<input type="text" id="s_new_food_type"/>
				<input type="button" id="s_new_type_btn" value="提交"/>
			</div>
		</div>
	</div>
</div>
<script src='/js/shanghu/new_food.js'></script>
	<script type="text/javascript">
var food_array = new Array();

$(document).ready(function(){
	again();
	var store_id = '<?=$store_id?>';
    $("#s_new_type_btn").click(function() {
		var val = $("#s_new_food_type").val();
		var en_val = encodeURI(val);
		$.ajax({
			type: "GET",
			url: "/shanghu/new_type/"+store_id+'/'+en_val,
			data:{},
			async:false,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
	        dataType: "json",
	        success: function(data){
	        	var l = $(".s_right").children().last();
	        	l.children().last().collapse('hide');

	        	var t = data.id;
	        	var h = "<div class='s_one_food_type accordion-group'> \
		        			<div class='s_food_type_name accordion-heading black_a'> \
								<a class='food_type_title'data-toggle='collapse'data-parent='.s_right' \
								   data-target='#s_food_in_"+t+"'>"+
								   val+
								   "<i></i> \
								</a> \
								<a class='delete_food_type' food-type='"+t+"'>删除这一种类</a> \
							</div> \
							<div class='s_food_in accordion-body collapse' id='s_food_in_"+t+"'> \
								<div class='s_food'> \
									<div class='s_add_one black_a'> \
										<a class='a_s_add_one' title='加菜' value='"+t+"'>+</a> \
									</div> \
								</div> \
							</div> \
						</div>";
	        	l.before(h);
	        	$("#s_food_in_"+t).collapse('show');
	        },
	        error: function(){
	        	alert("error");
	        }
		});
		again();
	});
});
    function get_o(o) {
		return $(o).parent().parent().next().next().children();
	}
	function i_get_o(o) {
		return $(o).parent().parent().next().children();
	}
	function loading(o) {
       	o.html("<img src='/img/search-loading.gif'/>");
	}
	function a_success(o, e) {
		o.html(e);
	}
	function a_error(o) {
		o.html('网络错误');
	}
	function a_wrong(o, e) {
		o.html(e);
	}

	var my_op = {
		'0': 'food_name', 
		'1': 'price', 
		'2': 'food_note'
	};
	function ajax_load_img(io) {
		var id = $(io).attr('id');
		var o = $(io).prev().prev();
		var b = get_o(io);
        $("#"+id).ajaxSubmit({ 
            dataType:  'json', //数据格式为json 
            // contentType: "application/x-www-form-urlencoded; charset=utf-8",
            beforeSend: function() { //开始上传 
               loading(b);
            }, 
            success: function(j) { //成功
            	// var j = JSON.parse(data);
                a_success(b,'');
                if (j.state){
                	o.attr("src", "/img/food/"+j.path);
                }
                else
                	alert(j.error);
                	// a_success(b, j.error);
                
            }, 
            error:function(xhr){ //上传失败 
                alert('上传失败')
            }
        });
	}
	
	function lang(op) {
		return my_op[op];
	}
	function again() {
		$(".avail").unbind('click');
		$('.s_i').unbind('focus');
		$('.s_i').unbind('blur');
		$(".a_s_add_one").unbind('click');
		$(".s_delete_one").unbind('click');
		$(".upload_dish").unbind('click');
		$('.img_upload_btn').unbind('click');
		$('.delete_food_type').unbind('click');
	    $('.avail').click(function() {
			var food_id=$(this).attr('name');
			var avail = $(this).attr('value');
			var b = $(this).parent().parent().prev().children().last().children();
			$.ajax({
				type: "GET",
				url: "/shanghu/ajax/"+store_id+"/food_avail/"+food_id+'/'+avail,
				data:{},
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
	            dataType: "json",
	            beforeSend: function(){
		            	loading(b);
		            },
		            success: function(){
		            	a_success(b,'');
		            },
		            error: function(){
		            	a_error(b);
		        }
			});
		});
		$('.s_i').bind("focus", function() {
			if (typeof $(this).attr('food') == 'undefined') 
				return;
			var food_id =$(this).attr('food');
			var op = $(this).attr('op');
			if (typeof food_array[food_id] == 'undefined') {
				food_array[food_id] = new Array();
				food_array[food_id][op] = $(this).val();
			}
			else {
				// if (typeof food_array[food_id][op] == 'undefined')
				// 	
				food_array[food_id][op] = $(this).val();
				
			}
		});
		$('.s_i').bind("blur", function() {
			if (typeof $(this).attr('food') == 'undefined') 
				return;
			var food_id=$(this).attr('food');
			var new_food_op=$(this).val();
			new_food_op = encodeURI(new_food_op);
			var op=$(this).attr('op');
			var b = i_get_o(this);
			if(food_array[food_id][op] != new_food_op) {
				$.ajax({
					type: "GET",
					url: "/shanghu/ajax/"+store_id+'/'+lang(op)+'/'+food_id+'/'+new_food_op,
					data:{},
					contentType: "application/x-www-form-urlencoded; charset=utf-8",
		            dataType: "json",
		            beforeSend: function(){
		            	loading(b);
		            },
		            success: function(d){
		            	a_success(b,'');
		            	if (!d.state)
		            		alert(d.error);
		            },
		            error: function(){
		            	a_error(b);
		            }
				});
			}
		});
		$(".a_s_add_one").click(function(){
			var o = $(this).parent().parent();
			var old = o.html();
			o.html("<div class='s_food_left' food=''> \
						<div> \
							<form name='form' action='/shanghu/img_upload/"+"<?=$store_id?>"+"' id=''method='post' enctype='multipart/form-data'> \
								<img class='s_food_img'src=''> \
								<input id=''name=''class='img_upload' type='file' size='100'  style='width:200px'/> \
								<input type='button' class='upload_dish' id=''value='上传菜说明'/> \
							</form> \
						</div> \
						<div> \
							<div> \
								菜名<input op='0'class='s_i s_i_food_name'type='text' value=''> \
							</div> \
							<div> \
								价格<input op='1'class='s_i s_i_price'type='text' value=''> \
							</div> \
							<div> \
								备注<input op='2'class='s_i s_i_note' type='text' value=''> \
							</div> \
						</div> \
						<div> \
							<span class='s_a_state'></span> \
						</div> \
					</div> \
					<div class='s_food_right'> \
						<div class='black_a'> \
							<a class='s_delete_one'>x</a> \
						</div> \
						<div> \
							<input type='radio' value='1'class='avail'name='avail' checked> \
							<label>有</label> \
						</div> \
						<div> \
							<input type='radio' value='0'class='avail'name='avail'> \
							<label>无</label> \
						</div> \
					</div>");
			o.parent().append("<div class='s_food'>"+old+
					 		  "</div>");
			again();
		});
		$(".s_delete_one").click(function() {
			var food_id = $(this).attr('food');
			if (typeof food_id != 'undefined') {
				var l = $(this).parent().parent().prev().find('.s_a_state');
				$.ajax({
					type: "GET",
					async : false,
					url: "/shanghu/delete_food/"+store_id+'/'+food_id,
					data:{},
					contentType: "application/x-www-form-urlencoded; charset=utf-8",
		            dataType: "json",
		            beforeSend: function(){
		            	loading(l);
		            },
		            success: function(d){
		            	a_success(l,'');
		            },
		            error: function(){
		            	a_error(l);
		            }
				});
			}
			$(this).parent().parent().parent().detach();
		});
		$(".upload_dish").click(function() {
			var arr = new Array();
			var o = $(this).parent().parent();
			o.next().children().each(function() {
				var v = $(this).children('.s_i').val();
				arr.push(v);
			});
			o.parent().next().find(".s_delete_one").detach();
			var f =o.parent().parent().parent();
			var food_type = f.attr('id');
			var l = get_o(this);
			var v = o.parent().next().find(":radio[name='avail'][checked]").val();
			var id;
			var m = $(this);
			if (arr[0] == "" || arr[1] == "") {
				alert("输入不能为空");
				return;
			}
			arr[0] = encodeURI(arr[0]);
			arr[1] = encodeURI(arr[1]);
			arr[2] = encodeURI(arr[2]);
			$.ajax({
				type: "GET",
				async : false,
				url: "/shanghu/add_dish/"+store_id+'/'+food_type+'/'+v+'/'+arr[0]+'/'+arr[1]+'/'+arr[2]+'/',
				data:{},
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
	            dataType: "json",
	            beforeSend: function(){
	            	loading(l);
	            },
	            success: function(d){
	            	a_success(l,'');
	            	id=d.id;
	            	if (id == 0) {
	            		alert(d.error);
	            		return;
	            	}
	            	m.attr('id', id);
	            	m.attr('class', 'img_upload_btn');
	            	m.attr('value', '修改图片');
	            	m.prev().attr('id', "img_upload_"+id);
	            	m.prev().attr('name', "img_upload_"+id);
	            	m.parent().attr('id', id);
	            	m.parent().parent().next().children().each(function() {
	            		$(this).find(".s_i").attr('food', id);
	            	});
	            	var k = m.parent().parent().parent();
	            	k.attr('food', id);
	            	k.next().find(':radio').attr('name', id);
	            },
	            error: function(){
	            	a_error(l);
	            }
			});
			ajax_load_img(this);
		});

		$('.img_upload_btn').click(function() {
			ajax_load_img(this);
		});
		$('.delete_food_type').click(function() {
			var type_id = $(this).attr('food-type');
			var o = $(this).parent().parent();
			if (typeof type_id != 'undefined') {
				$.ajax({
					type: "GET",
					async : false,
					url: "/shanghu/delete_food_type/"+store_id+'/'+type_id,
					data:{},
					contentType: "application/x-www-form-urlencoded; charset=utf-8",
		            dataType: "json",
		            success: function(d){
		            	if (d.state){
		            		o.detach();
		            	}
		            	else
		            		alert('无法删除该种类');
		            }
				});
			}
		});
	}

	</script>
</div>
</div>
