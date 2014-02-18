<?php
	function food_avai($food) {
		if ($food['isAvailable'] == '1')
			echo "checked";
	}
	function not_food_avai($food) {
		if ($food['isAvailable'] != '1')
			echo "checked";
	}
?>

<script src="/js/jquery.form.js"></script>
<script src='/js/urldecode.js'></script>
<div class="main">
	<div class="head_container black">
		<div class='navinfo'>
			<span class='s_name'><?=$s_name?></span>
			<span class="s_state black_a">
		        <a class="nav-sstate" data-toggle="dropdown" id="nav-sstate">
		          <span value="<?=$now_state_id?>"class='now_state'><?=$now_state_name?></span>
		          <i class="icon-dropdown"></i>
		        </a>
		        <ul class="dropdown-menu " id="sstate-dropdown">
		          <?php
		          foreach ($all_state as $key=>$state_name) { ?>
		          	<li><a class='s_state_c'value="<?=$key?>"><?=$state_name?></a></li>
		          <?php
		      	  } ?>
		        </ul>
		    </span>
		    <span class='you_have_new_order black_a'>
				<a class='note_new_sale'>您有<span class='new_sale_num'></span>份新订单</a>
		    	<div id="new_sale" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				    <h3 id="h-order-confirm">查看新订单</h3>
				  </div>
				  <div class="modal-body sale_info">
				  </div>
				  <div class="modal-footer">
				    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
				  </div>
				</div>
		    	<bgsound id="bgs" src="" loop=1>
		    </span>
		    <span class='nav_loading'></span>
		    <span class='nav_note'></span>
		    <script type="text/javascript">
		    	var store_id = "<?=$store_id?>";
		    </script>
			<script src='/js/shanghu/nav.js'></script>
			<div class='black_a m_shanghu_left'>
				<a href="/shanghu/food_manage/<?=$store_id?>">食物管理</a></span>
				<a href="/shanghu/store_info_manage/<?=$store_id?>">商店信息管理</a></span>
				<!-- <span class='black_a'><a href="/shanghu/<?=$store_id?>/">查看留言</a></span> -->
				<a href="/shanghu/sale_info/<?=$store_id?>/">查看历史订单</a>
				<a href="/shanghu/change_type_order/<?=$store_id?>/">调整食物种类顺序,名字</a>
				<a href="/shanghu/change_food_order/<?=$store_id?>/">调整食物顺序</a>
				<a href="/shanghu/change_passwd/<?=$store_id?>/">修改密码</a>
				<a href="/shanghu/cancel_sale/<?=$store_id?>/">取消订单</a>
				<a href="/shanghu/check_cancel_sale/<?=$store_id?>/">查看正在取消的订单</a>
			</div>
		</div>

