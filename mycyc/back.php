// $(".order-one").bind("taphold", function(event) {
// 	foodId=$(this).attr("food-id");
// 	foodPrice=$(this).attr("food-price");
// 	foodInfo = {
// 		foodId : foodId,
// 		foodPrice : foodPrice
// 	};

// 	chooser=".no_f_symbol[food-id='"+foodId+"']";
// 	isChecked=$(chooser).html();
// 	if(isChecked){
// 		//这里没法用Array.indexOf(),就自己写个循环找一下
// 		for(i=0; i<order.length; i++)
// 			if(order[i].foodId == foodId){
// 				deletePos = i;
// 				break;
// 			}
// 		if(deletePos < order.length)
// 			order.splice(deletePos, 1);
// 		$(chooser).empty();
// 	}
// 	else{
// 		order.push(foodInfo);
// 		$(chooser).append("<span class='glyphicon glyphicon-ok'></span>");
// 		$("#hint").empty();
// 	}
// 	if(order.length==0){
// 		$.cookie('mycyc_order', JSON.stringify(order), {expires:-1, path:'/'});
// 		$.cookie('mycyc_order_store', JSON.stringify(orderStore), {expires:-1, path:'/'});
// 	}
// 	else{
// 		$.cookie('mycyc_order', JSON.stringify(order), {expires:30, path:'/'});
// 		$.cookie('mycyc_order_store', JSON.stringify(orderStore), {expires:30, path:'/'});
// 	}
// });

// $("#reset-button").bind("taphold", function(event) {
// 	e.preventDefault();
// 	$(".no_f_symbol").empty();
// });

// $("#confirm-button").bind("taphold", function(event) {
// 	e.preventDefault();
// 	if(order.length==0){
// 		if(!$("#hint").html())
// 			$("#hint").append("你还没有选择任何食物呢");
// 	}
// 	else{
// 		if (cannot_submit == 1)
// 			$('#basket').modal('show');
// 		else
// 			$("#order-form").submit();
// 	}
// });

<?php
	// if (@$another_one) { ?>
	// 	$(document).ready(function() {
	// 		var another_food = '<?=$another_one?>';
	// 		var food_arr = another_food.split('-');
	// 		for (var i in food_arr) {
	// 			$("a[food-id="+food_arr[i]+"]").trigger('taphold');
	// 		}
	// 	});
	// <?php
	// }


