<div class='m_right'>
	<h3>修改商店状态</h3>
	<div>
			<?php
			if (@$university) {
				foreach ($university as $univ) { ?>
				<div class='black_a'>
					<a href="change_store_state/<?=$univ['schoolId']?>"><?=$univ['schoolFullName']?></a>
				</div>
				<?php
				} 
			}
			else if (@$store) {
				foreach ($store as $one_store) { ?>
					<div class='black_a'>
						<a href="<?=$university_id?>/<?=$one_store['store_id']?>"><?=$one_store['store_name']?></a>
						<?php
						if ($one_store['state'] == '5')
							echo "(已删除)";
						?>
					</div>
				<?php
				}
			}
			else if (@$delete) { ?>
				<div class='black_a'>
					<a href='<?=$store_id?>/1'>确认删除该店</a>
				</div>
				<div class='black_a'>
					<a href='<?=$store_id?>/2'>确认重新增加该店</a>
				</div>
				<form action='/rooter/change_block' method='post' target='_blank'>
					<input type='hidden' name='store_id' value="<?=$store_id?>"/>
					<?php
				$i = 0;
				foreach ($block_info as $block) { ?>
					<div>
					<?=$block['block_name']?>
						<input type='checkbox' name='delivery_order[]' value="<?=$block['block_num']?>" <?php 
							if ( ($now_block & $block['block_num']) != 0) echo "checked"; ?> />
					</div>
				<?php
				} ?>
					<input type='submit' value='确认修改区域'>
				</form>
			<?php
			}
			else { ?>
				<div class='black_a'>
					<h4>修改成功</h4>
					<a href='/rooter/change_store_state'>继续修改</a>
				</div>
			<?php
			} ?>
	</div>
	
</div>
</div>
</div>