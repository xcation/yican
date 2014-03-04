<?php
	$redirectUrl = $this->input->get('rUrl');
	$this->load->helper('cookie');
	delete_cookie("mycyc_order");
	delete_cookie("mycyc_order_store");
	header('Location:'.$redirectUrl);
?>