<div class='m_right'>
	<h3>修改商店顺序</h3>
	<div>
			<?php
			if (@$university) {
				foreach ($university as $univ) { ?>
				<div class='black_a'>
					<a href="change_store_order/<?=$univ['schoolId']?>"><?=$univ['schoolFullName']?></a>
				</div>
				<?php
				} 
			}
			else if (@$store_delivery_order) { ?>
				<form action='/rooter/change_store_order/<?=$university_id?>' method='post'>
					<?php
					foreach ($store_delivery_order as $i => $store) { 
						if ($i == 0 ) { ?>
							<h5>外卖</h5>
						<?php
						}
						else { ?>
							<h5>预定</h5>
						<?php
						} 
						$total = count($store) - 1;
						foreach($store as $key => $type) { 
							if ($type['state'] == '5')
								continue; 
							?>
							<div>
								<span class='black_a'>
									<?php
									if ($key == 0) { ?>
										<a class='down'>↓</a>
									<?php
									}
									else if ($key == $total) { ?>
										<a class='up'>↑</a>
									<?php
									}
									else {  ?>
										<a class='up'>↑</a>
										<a class='down'>↓</a>
									<?php
									} ?>
								</span>
								<input type='hidden' name='<?php if ($i == 0)
																	echo "store_order_0[]";
																 else
																 	echo "store_order_1[]";?>' value='<?=$type['store_id']?>' />
								<span class='type_name'><?=$type['store_name']?></span>
							</div>
					<?php }
					} ?>
					<input type='submit' class='btn' value='提交修改'/>
				</form>

			<?php
			} ?>
			
	</div>
	<script type="text/javascript">
	<?php
			if (@$post) { ?>
				alert('修改成功');
		<?php
		} ?>
		function swap(obj, next_obj, now, next, now_text, next_text) {
			$(obj).next().val(next);
			$(next_obj).find(":hidden").val(now);
			$(obj).next().next().text(next_text);
			$(next_obj).find("span.type_name").text(now_text);
		}
		$('.up').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var prev_obj = $(o).parent().prev();
			var prev = prev_obj.find(":hidden").val();
			var now_text = $(o).next().next().text();
			var prev_text = prev_obj.find('span.type_name').text();
			swap(o, prev_obj, now, prev, now_text, prev_text);
		});
		$('.down').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var next_obj = $(o).parent().next();
			var next = next_obj.find(":hidden").val();
			var now_text = $(o).next().next().text();
			var next_text = next_obj.find('span.type_name').text();
			swap(o, next_obj, now, next, now_text, next_text);
		});
	</script>
</div>
</div>
</div>