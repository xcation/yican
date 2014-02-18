function nav_loading() {
	$('.nav_note').text('');
	$('.nav_loading').html("<img src='/img/search-loading.gif' />");
}
function nav_success() {
	$('.nav_loading').html('');
	$('.nav_note').text('状态更新成功');
}
function nav_error() {
	$('.nav_loading').html('');
	// $('.nav_note').text('状态更新失败');
}

$(document).ready(function(){
	function check_new_sale() {
		$.ajax({
			type: "GET",
			url: "/shanghu/check_new_sale/"+store_id,
			data:{},
			async:false,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
	        dataType: "json",
	        success: function(data){
	        	if (data.state == 1) { //有订单
	        		$(".new_sale_num").text(data.num);
	        		$('.note_new_sale').css("display", "inline");
	        		$('#bgs').attr('src', "/sound/msg.wav");
	        		$('.sale_info').html(urldecode(data.html));
	        	}
	        	else {
	        		$('.note_new_sale').css("display", "none");
	        	}
	        },
	        error: function(){
	        	// alert("error");
	        }
		});
		$(".received").click(function(){
			var sale_id = $(this).attr('sale');
			var o = this;
			$.ajax({
				type: "GET",
				url: "/shanghu/check_received/"+sale_id,
				data:{},
				async:false,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
		        // dataType: "json",
		        beforeSend:function() {
		        	$(o).parent().next().children().attr('src','/img/search-loading.gif');
		        },
		        success: function(){
		        	$(o).parent().next().children().attr('src','');
		        	//有订单
		        	$(o).parent().next().next().text('确认成功');
		        },
		        error: function(){
		        	
		        	alert("error");
		        }
			});
		});
	}
	check_new_sale();
	setInterval(check_new_sale, 30000);

	$('.note_new_sale').click(function(){
		$('#new_sale').modal('show');
	});
	$(".nav-sstate").dropdown();
	$('.s_state_c').click(function() {
		var now_s = $(this).attr('value');
		$('.now_state').text($(this).text());
		$('.now_state').attr('value', now_s);
		$.ajax({
				type: "GET",
				url: "/shanghu/change_state/"+store_id+'/'+now_s,
				data:{},
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
	            // dataType: "json",
	            beforeSend: function(){
		            nav_loading();
		        },
		        success: function(){
	            	nav_success();
	            },
	            error: function(){
	            	nav_error();
		        }
		});
	});
});