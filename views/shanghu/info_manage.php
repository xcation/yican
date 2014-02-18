<div class='s_right'>
	修改商店信息：
	<form action="/shanghu/post_store_info/<?=$store_info['store_id']?>" method='post'enctype="multipart/form-data">
		<div>
			<label>商店名</label>
			<input type='text' name='store_name' id='store_name' value="<?=$store_info['store_name']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>商店地址</label>
			<input type='text' name='store_loc' id='store_loc' value="<?=$store_info['store_loc']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>商店图片</label>
			<img src="/img/store/<?=$store_info['store_img']?>">
			<input type='file' name='store_img' id='store_img'/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>联系电话</label>
			<input type='text' name='store_tel_1'id='store_tel_1' value="<?=$store_info['store_tel_1']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>备用电话1</label>
			<input type='text' name='store_tel_2'id='store_tel_2' value="<?=$store_info['store_tel_2']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>备用电话2</label>
			<input type='text' name='store_tel_3'id='store_tel_3' value="<?=$store_info['store_tel_3']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			<label>营业时间</label>
			<?php
				function echo_hour($hour) {
					for($i = 0; $i < 24; $i++) { 
						$k = "";
						if ($i < 10)
							$k = "0";
						$k .= $i; ?>

						<option value="<?=$i?>" 
							<?php
								if ($k==$hour)
									echo "selected='selected'";
							?>
						>
						<?=$k?>
						</option>
				<?php
					}
				}
				function echo_minite($minite) { ?>
					<option value='0' 
					<?php 
						if ($minite == '00')
							echo "selected='selected'";
					?>
					>00</option>
					<option value='30'
					<?php 
						if ($minite == '30')
							echo "selected='selected'";
					?>
					>30</option>
				<?php
				}
				?>
			<?php
			for ($i = 0; $i < 2; $i++) { ?>
				<div>
					<select name="start_hour[]" style="width:auto;height: auto">
						<?php echo_hour($store_info['start_hour'][$i]); ?>
					</select>
					:
					<select name="start_minite[]>" style="width:auto;height: auto">
						<?php echo_minite($store_info['start_minite'][$i]); ?>
					</select>
					<span class='time_to'>-</span>
					<select name='end_hour[]' style="width:auto;height: auto">
						<?php echo_hour($store_info['end_hour'][$i]); ?>
					</select>
					:
					<select name='end_minite[]?>' style="width:auto;height: auto">
						<?php echo_minite($store_info['end_minite'][$i]); ?>
					</select>
				</div>
			<?php
			} ?>
			<span class='m_error'></span>
		</div>
		<div class='n_block'>
			<label>服务的学校</label>
			<?php
			foreach($univ_info as $univ) { ?>
			<div>
				<input type='checkbox'class='university'name='university_id[]' 
					   value="<?=$univ['schoolId']?>"
					   <?php
					   if ($univ['checked'])
					   		echo "checked";
					   ?> 
				/><?=$univ['schoolFullName']?>
				<span style="height:30px">
					<?php
					if ($univ['checked']) { ?>
						起送价
						<input style="width:40px;height:30px" type='text' 
						class='delivery_cost_need' name='delivery_cost[]'
						value="<?=$univ['delivery_cost']?>"/>
					<?php
					} ?>
				</span>
			</div>
			<?php } ?>
		</div>
		<div>
			<label>修改交易方式</label>
			外卖<input type='radio' name='deliver_order' 
				<?php
					if ($store_info['order_type']=='0')
						echo "checked";
				?> value='0'>
			预订<input type='radio' name='deliver_order' 
				<?php
					if ($store_info['order_type']=='1')
						echo "checked";
				?> value='1'>
		</div>
		
		<div>
			公告信息（最多100个字）
			<textarea colomn='20' row='5' name='gonggao' id='gonggao'><?php
					if ($store_info['gonggao'])
						echo $store_info['gonggao'];
			?></textarea>
			<span class='m_error'></span>
		</div>
		<div>
			商店简介（最多100个字）
			<textarea colomn='20' row='5' name='brief_intro' id='brief_intro'><?php
					if ($store_info['brief_intro'])
						echo $store_info['brief_intro'];
			?></textarea>
			<span class='m_error'></span>
		</div>
		<div>
			起送价简介（最多40个字）
			<textarea colomn='20' row='2' name='deliver_note' id='deliver_note'><?php
					if ($store_info['deliver_note'])
						echo $store_info['deliver_note'];
			?></textarea>
			<span class='m_error'></span>
		</div>
		<div>
			营业时间简介（最多40个字）
			<textarea colomn='20' row='2' name='open_time_note' id='open_time_note'><?php
					if ($store_info['open_time_note'])
						echo $store_info['open_time_note'];
			?></textarea>
			<span class='m_error'></span>
		</div>
		<div>
			联系方式简介（最多40个字）
			<textarea colomn='20' row='2' name='contact_phone_note' id='contact_phone_note'><?php
					if ($store_info['contact_phone_note']) {
						// echo "13";
						echo $store_info['contact_phone_note'];
					}
			?></textarea>
			<span class='m_error'></span>
		</div>
		<div>
			最高订单量
			<input type='text' style='width:40px'name='max_order' id='max_order'value="<?=@$store_info['max_order']?>"/>
			<span class='m_error'></span>
		</div>
		<div>
			主营方向选择
			<?php
			foreach($store_type_info as $type_info) { ?>
				<input type='checkbox' name='store_type[]' value="<?=$type_info['store_type_id']?>"
					<?php
					if ($type_info['checked']) 
						echo "checked";
					?>
				/>
				<?=$type_info['store_type_name']?>
			<?php
			} ?>
		</div>
		<div>
			<input type='submit'value='提交'/>
		</div>

	</form>
	<script type="text/javascript">
	$('.university').click(function(){
		var h;
		if (typeof $(this).attr('checked') == 'undefined') 
			h = "";
		else
			h = "起送价 \
				<input style='width:40px;height:30px' type='text' class='delivery_cost_needed' name='delivery_cost[]'/>";
		$(this).next().html(h);
	});

	$(document).ready(function(){
		<?php 
			if (isset($error)) {
				echo "alert('{$error}');";
				echo "window.location.href='/shanghu/store_info_manage/{$store_info['store_id']}';";
			}
		?>
	});
	</script>
</div>
</div>
</div>
