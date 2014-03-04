<?php
	$this->load->helper('cookie');
	delete_cookie("mycyc_user");
	header("location: ".constant('mycycbase'));
?>