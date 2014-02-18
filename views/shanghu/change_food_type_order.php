<div class='s_right'>
	<form action='/shanghu/change_type_order/<?=$store_id?>' method='post'>
	<?php
		$total = count($all_type) - 1;
		foreach($all_type as $key => $type) { ?>
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
				<input type='hidden' name='type_order[]' value='<?=$type['food_type_id']?>' />
				<input type='text' name='type_name[]'class='type_name' value='<?=@$type['food_type_name']?>' />
			</div>
	<?php } ?>
		<input type='submit' class='btn' value='提交修改'/>
	</form>
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
			$(obj).next().next().val(next_text);
			$(next_obj).find(".type_name").val(now_text);
		}
		$('.up').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var prev_obj = $(o).parent().prev();
			var prev = prev_obj.find(":hidden").val();
			var now_text = $(o).next().next().val();
			var prev_text = prev_obj.find('.type_name').val();
			swap(o, prev_obj, now, prev, now_text, prev_text);
		});
		$('.down').click(function() {
			var o = $(this).parent();
			var now = $(o).next().val();
			var next_obj = $(o).parent().next();
			var next = next_obj.find(":hidden").val();
			var now_text = $(o).next().next().val();
			var next_text = next_obj.find('.type_name').val();
			swap(o, next_obj, now, next, now_text, next_text);
		});
	</script>
</div>
</div>
