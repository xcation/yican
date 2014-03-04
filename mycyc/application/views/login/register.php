<div class="tab-pane" id="register">
	<form id='f_register' action='<?=constant('mycycbase')?>/register' method='post'>
	  	<div class="control-group info">
		  <div class="control-label" for="reg_login_id">用户名</div>
		  <div class="controls">
		    <input type="text" id="reg_login_id" class='form-control'name='reg_login_id'>
		    <span class="login_warn">
		    	<?php
		    		if (isset($phone))
		    			echo $phone;
		    	?>
		    </span>
		  </div>
		</div>
		<div class="control-group info">
		  <div class="control-label" for="reg_passwd" >密码(6-16位之间)</div>
		  <div class="controls">
		    <input type="password" id="reg_passwd" class='form-control'name='reg_passwd' datatype="*6-16"nullmsg="请输入密码"errormsg="密码范围在6~16位之间！">
		    <span class="login_warn"></span>
		  </div>
		</div>
		<div class="control-group info">
		  <div class="control-label" for="reg_passwd_con" datatype="*"recheck='reg_passwd'>密码确认</div>
		  <div class="controls">
		    <input type="password" id="reg_passwd_con" name='reg_passwd_con' class='form-control'>
		    <span class="login_warn"></span>
		  </div>
		</div>
		
		<div>
			<input type='submit' class='btn btn-primary'value='注册'>
		</div>
	</form>
</div>
<script type="text/javascript">
$("#f_register").validate(
		{
		rules: 
		{ 
			reg_login_id: { 
				required: true, 
				rangelength:[6,30],
				remote: { 
					url: "<?=constant('mycycbase')?>/register/check_userid", 
					type: "post", 
					dataType: "json",
					contentType: "application/x-www-form-urlencoded; charset=utf-8"
				}
			}, 
			reg_passwd: 
			{ 
				required: true, 
				rangelength: [6, 30]
			},
			reg_passwd_con:
			{
				required: true,
				equalTo:"#reg_passwd"
			}
		}, 
		messages: 
		{ 
			reg_login_id: 
			{
				required: function() {
							return lang('ls_input_myb');
						  }, 
				rangelength:lang('ls_tel_length'),
				remote: "该用户名已经注册"
			}, 
			reg_passwd: { 
				required: lang('ls_login_password'), 
				rangelength: $.format(lang('ls_password_length')) 
			},
			reg_passwd_con: {
				required:lang('ls_login_password'), 
				equalTo: $.format(lang('ls_login_password_rep'))
			},
		}, 
		errorPlacement: function(error, element) { 
			var placement = $(element.next()); 
			// $element.parent().parent().removeClass()
			placement.text(''); 
			error.appendTo( placement );
			var t = element.parent().parent();
			t.removeClass('info');
			t.addClass('error');
		}, 
		onkeyup: false,
		submitHandler: function(form){
		    form.submit();
		},
		success: function(label) {
		    // set   as text for IE
		    var t = label.parent().parent().parent();
		    t.removeClass('error');
			t.addClass('info');
		    //label.addClass("valid").text("Ok!")
		}
		}); 