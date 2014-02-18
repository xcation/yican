<div class='m_right'>
	<script src="/js/jquery.validate.js"></script>
	<script src="/js/additional-methods.js"></script>
	<div class='m_new_store'>
		<span><?=@$error?></span>
		<form id='store_register' enctype="multipart/form-data" action='/rooter/new_store' method='post'>
			<div class='n_block'>
				<label>用户名</label>
				<input type='text' name='store_login_id' value="<?=@$post['store_login_id']?>"id='store_login_id'/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>密码</label>
				<input type='password' name='store_passwd'id='store_passwd'>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>确认密码</label>
				<input type='password' name='store_passwd_confirm'id='store_passwd_confirm' />
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>联系电话</label>
				<input type='text' name='store_tel'id='store_tel' value="<?=@$post['store_tel']?>"/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>备用电话1</label>
				<input type='text' name='store_tel_2'id='store_tel_2' value="<?=@$post['store_tel_2']?>"/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>备用电话2</label>
				<input type='text' name='store_tel_3'id='store_tel_3' value="<?=@$post['store_tel_3']?>"/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>店铺名称</label>
				<input type='text' name='store_name'id='store_name' value="<?=@$post['store_name']?>"/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>店铺地址</label>
				<input type='text' name='store_loc'id='store_loc' value="<?=@$post['store_loc']?>"/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>选择服务的学校</label>
				<?php
				foreach($university as $univ) { ?>
				<div>
					<input type='checkbox'class='university'name='university_id[]' value="<?=$univ['schoolId']?>"/><?=$univ['schoolFullName']?>
					<span class=''style="height:30px">
					</span>
				</div>
				<?php } ?>
			</div>
			
			<div class='n_block'>
				<label>店铺图片</label>
				<input type='file' name='store_img' id='store_img'/>
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				<label>选择营业</label>
				<?php
					function echo_hour() {
						for($i = 0; $i < 24; $i++) { ?>
							<option value="<?=$i?>">
								<?php
								if ($i < 10)
									echo "0";
								echo $i; ?>
							</option>
					<?php
						}
					}
					function echo_minite() {
						echo "<option value='0'>00</option>".
							 "<option value='30'>30</option>";
					}
				?>
				<?php
				for ($i = 0; $i < 2; $i++) { ?>
				<div>
					<select name="start_hour[]" style="width:auto;height: auto">
						<?php echo_hour(); ?>
					</select>
					:
					<select name="start_minite[]>" style="width:auto;height: auto">
						<?php echo_minite(); ?>
					</select>
					<span class='time_to'>-</span>
					<select name='end_hour[]' style="width:auto;height: auto">
						<?php echo_hour(); ?>
					</select>
					:
					<select name='end_minite[]?>' style="width:auto;height: auto">
						<?php echo_minite(); ?>
					</select>
				</div>
				<?php
				} ?>
			</div>
			<div class='n_block'>
				<?php
				$i = 0;
				foreach ($block_info as $block) { ?>
					<?=$block['block_name']?>
					<input type='checkbox' name='delivery_order[]' value="<?=$block['block_num']?>" <?php if ($i++ == 0) echo "checked"; ?> />
				<?php
				} ?>
			</div>
			<div class='n_block'>
				最高订单量
				<input type='text' style='width:40px'name='max_order' id='max_order'value="<?=@$post['max_order']?>"/>				
				<span class='m_error'></span>
			</div>
			<div class='n_block'>
				主营方向选择
				<?php
				foreach($store_type_info as $type_info) { ?>
				<div>
					<input type='checkbox' name='store_type[]' value="<?=$type_info['storeTypeId']?>"/>
					<?=$type_info['storeTypeName']?>
				</div>
				<?php
				} ?>
			</div>
			<div class='n_block'>
				<input type='submit' value='提交'/>
			</div>
		</form>
	</div>
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
	var not_empty="不能为空";
	$("#store_register").validate(
	{
		rules: 
		{
			store_login_id: { 
				required: true, 
				rangelength:[6, 30],
				remote: { 
					url: "/rooter/check_store_login_id", 
					type: "post", 
					dataType: "json",
					contentType: "application/x-www-form-urlencoded; charset=utf-8"
				}
			},
			store_passwd: 
			{
				required: true, 
				rangelength: [6, 30]
			},
			store_passwd_confirm:
			{
				required: true,
				equalTo:"#store_passwd"
			},
			store_tel: {
				required: true,
				rangelength: [11, 11]
			},
			store_tel_2: {
				rangelength: [11, 11]
			},
			store_tel_3: {
				rangelength: [11, 11]
			},
			store_name: {
				required:true,
				remote: {
					url: "/rooter/check_store_name", 
					type: "post", 
					dataType: "json",
					contentType: "application/x-www-form-urlencoded; charset=utf-8"
				}
			},
			store_loc: {
				required:true
			},
			// store_delivery_cost: {
			// 	required:true,
			// 	digits:true
			// },
			store_img:{
				required:true
			},
			max_order:{
				required:true,
				digits:true
			}
		}, 
		messages: 
		{ 
			store_login_id: { 
				required: "用户名"+not_empty, 
				rangelength:"长度在6~30之间",
				remote: "用户名重复"
			},
			store_passwd: 
			{
				required: "密码"+not_empty,
				rangelength: "长度在6~30之间"
			},
			store_passwd_confirm:
			{
				required: not_empty,
				equalTo: "两次密码不一致"
			},
			store_tel: {
				required: "联系方式"+not_empty,
				rangelength: "长度为11位"
			},
			store_tel_2: {
				rangelength: "长度为11位"
			},
			store_tel_3: {
				rangelength: "长度为11位"
			},
			store_name: {
				required: "店铺名"+not_empty,
				remote: "店名重复",
			},
			store_loc: {
				required: "地址"+not_empty
			},
			// store_delivery_cost:{
			// 	required: "起送价"+not_empty,
			// 	digits:"请输入数字"
			// },
			store_img:{
				required: "不能为空"
			},
			max_order:{
				required: not_empty,
				digits:"必须为是数字"
			}
		}, 
		errorPlacement: function(error, element) { 
			var placement = $(element.next()); 
			// $element.parent().parent().removeClass()
			placement.text(''); 
			error.appendTo( placement );
		}, 
		onkeyup: false,
		onfocusOut: true,
		submitHandler: function(form){
			var invalid = false;
			$('.delivery_cost_needed').each(function(){
				if ($(this).val() == ""&&!invalid) {
					alert("资料未完全，无法正确提交");
					invalid = true;
					return;
				}
			});
			if (!invalid)
		    	form.submit();
		}
		
	}); 
	</script>
</div>
</div>
</div>
