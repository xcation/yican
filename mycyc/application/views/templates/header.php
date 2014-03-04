<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href="<?=constant("mycycbase")?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?=constant("mycycbase")?>/css/main.css" rel="stylesheet">
	<script src="<?=constant("mycycbase")?>/js/jquery-1.10.2.min.js"></script> 
	
<!--
	<link rel="stylesheet" type="text/css" href="<?=constant('mycycbase')?>/css/jquery.mobile.structure-1.1.1.css">
	<script src="<?=constant('mycycbase')?>/js/jquery.mobile-1.1.1.js"></script>
 -->
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<?php
	$json_order=$this->input->cookie('mycyc_order');
	if($json_order){
		$order = json_decode($json_order, true);
		$count = count($order);
		if($count > 0)
			$display = "(".$count.")";
		else
			$display = "";
	}
	else{
		$display = "";
	}
?>
<body>
	<div class="container">
		<div id="header">
			<div id="title">
				<span>
					一餐易餐 - 移动版
					<?php
						if($user=$this->input->cookie('mycyc_user')) { ?>
							欢迎<?=$user?>|<a href="<?=constant('mycycbase')?>/deletecookie3">注销</a>;
						<?php
						}
						else { ?>
							<a href="<?=constant('mycycbase')?>/login">登录</a>&nbsp;
							<a href="<?=constant('mycycbase')?>/register">注册</a>
						<?php
						}
					?>
				</span>
			</div>
			<div id="navi">
				<ul>
					<li><a href="<?=constant("mycycbase")?>">首页</a></li>
					<li>|</li>
					<li><a href="<?=constant("mycycbase")?>/order">我的订单</a></li>
					<li>|</li>
					<li><a href="<?=constant("mycycbase")?>/cart">美食篮子<?=$display?></a></li>
					<li>|</li>
				</ul>
			</div>
		</div>
		<div id="thin">