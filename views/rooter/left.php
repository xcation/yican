<div class='main'>
	<div class='head_container black'>
		<div class='m_left'>
			<?php
			if (@$super_root || @$region_root) { ?>
			<ul>
				<li><a href="#">区域管理</a></li>
				<ul>
					<?php
					if (@$super_root) { ?>
						<li><a href='/rooter/new_region'>增加区域</a></li>
					<?php }
					if (@$region_root) { ?>
						<li><a href="/rooter/new_univ">增加"大学"|"网吧"</a></li>
					<?php
					} ?>
				</ul>
			</ul>
			<?php
			}
			if (@$region_root || @$region_new_store || $super_root) { ?>
			<ul>
				<li><a href="#">店铺管理</a></li>
				<ul>
					<?php
					if (@$super_root) { ?>
						<li><a href="/rooter/new_store_type">新商店种类</a></li>
					<?php
					}
					if (@$region_root || @$region_new_store) { ?>
						<li><a href="/rooter/new_store">新商家注册</a></li>

					<?php
					}
					if (@$region_root) { ?>
						<li><a href="/rooter/change_store_state">修改商店状态</a></li>
						<li><a href="/rooter/change_store_order">修改商店顺序</a></li>
						<li><a href='/rooter/change_store_password'>修改商店密码</a></li>
					<?php } ?>
				</ul>
			</ul>
			<?php
			}
			if (@$region_root || @$super_root) { ?>
			<ul>
				<li><a href="#">账务管理</a></li>
				<ul>
					<li><a href='/rooter/sale_today'>查看今日订单</a></li>
					<li><a href='/rooter/show_deep_search'>调查</a></li>
					<li><a href='/rooter/sale_history'>查看订单状况</a></li>
				</ul>
			</ul>
			<?php
			}
			if (@$super_root) { ?>
			<ul>
				<li><a href="#">网站管理</a></li>
				<ul>
					<li><a href='/rooter/add_announce'>修改公告</a></li>
					<li><a href='/rooter/sms_configure'>修改短信</a></li>
					<li><a href="/rooter/sms_record">查看短信</a></li>
					<li><a href='/rooter/add_footer'>增加尾部信息</a></li>
					<li><a href='/rooter/pos'>商户pos端设置</a></li>
				</ul>
			</ul>
			<?php }
			if (@$region_root) { ?>
			<ul>
				<li><a href="/rooter/new_manage_guy">人员管理</a></li>
			</ul>
			<?php
			}
			if (@$region_root || @$super_root || @$region_new_store) { ?>
			<ul>
				<li><a href="/saleman" target="blank">订单管理</a></li>
			</ul>
			<?php } ?>
			<ul>
				<li><a href="/rooter/logout">退出</a></li>
			</ul>

		</div>
<script type="text/javascript">
   $(document).ready(function() {
		$(".m_left ul li").next("ul").hide();
		$(".m_left ul li").click(function() {
		    $(this).next("ul").toggle();
		});
	});
</script>
