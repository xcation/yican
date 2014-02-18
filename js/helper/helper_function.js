function get_cart() 
{
	var cart;
	if (cart = $.cookie('my_cart'))
		cart = JSON.parse(cart);
	else 
		cart = [];
	return cart;
}
function get_history_order() {
	var cart;
	if (cart = $.cookie('history_order'))
		cart = JSON.parse(cart);
	else 
		cart = [];
	return cart;
}