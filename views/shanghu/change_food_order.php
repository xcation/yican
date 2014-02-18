<div class='s_right'>
	<?php
		if (@$all_type) {
			foreach($all_type as $type) { ?>
				<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>/<?=$type['food_type_id']?>'><?=$type['food_type_name']?></a></div>
			<?php
			} ?>
			<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>'>返回</a></div>
		<?php
		}
		else {
			// 下面是有图片区
			if (@$img_or_not) { ?>
				<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>/<?=$food_type_id?>/1'>有图片区</a></div>
				<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>/<?=$food_type_id?>/0'>无图片区</a></div>
				<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>'>返回</a></div>
			<?php
			}
			else { ?>
				<form action='/shanghu/change_food_order/<?=$store_id?>/<?=$food_type_id?>/<?=$img?>' method='post'>
				<?php
					$total = count($all_food) - 1;
					if ($total >= 0) {
						if ($total == 0) { ?>
							<span name='food_name[]'class='food_name'><?=$all_food[0]['food_name']?>></span>
							<div class='black_a'><a href='/shanghu/change_food_order/<?=$store_id?>/<?=$food_type_id?>'>返回</a></div>
						<?php 
						}
						else { 
							foreach(@$all_food as $key=>$food) { ?>
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
									<input type='hidden' name='food_order[]' value='<?=$food['food_id']?>' />
									<span name='food_name[]'class='food_name'><?=$food['food_name']?></span>
								</div>
							<?php
							} ?>
							<input type='submit' class='btn' value='提交修改'/>
							<a class='black' href='/shanghu/change_food_order/<?=$store_id?>/<?=$food_type_id?>'>返回</a>
						<?php
						}
					} 
					else {
						echo "暂时没有食物";
					}?>
				</form>
			<?php
			} 
		}?>
					
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
			$(next_obj).find(".food_name").text(now_text);
		}
		$('.up').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var prev_obj = $(o).parent().prev();
			var prev = prev_obj.find(":hidden").val();
			var now_text = $(o).next().next().text();
			var prev_text = prev_obj.find('.food_name').text();
			swap(o, prev_obj, now, prev, now_text, prev_text);
		});
		$('.down').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var next_obj = $(o).parent().next();
			var next = next_obj.find(":hidden").val();
			var now_text = $(o).next().next().text();
			var next_text = next_obj.find('.food_name').text();
			swap(o, next_obj, now, next, now_text, next_text);
		});
	</script>
</div>
</div>
