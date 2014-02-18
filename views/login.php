<div class="main">
	<div class="head_container blank">
		<script src="/js/jquery.validate.js"></script>
		<script src="/js/additional-methods.js"></script>
		<div class='login'>
			<ul class="nav nav-tabs" id="myTab">
			  <li class='black_a <?php
			  	if (!$register)
			  		echo "active"; ?>' ><a href="/login/#login" data-toggle="tab">登录</a></li>
			  <li class='black_a <?php
			  	if ($register)
			  		echo "active"; ?>' ><a href="/login/#register" data-toggle="tab">注册</a></li>
			</ul>

			<div class="tab-content">
			  <div class="tab-pane 
			  	<?php
			  		if (!$register)
			  			echo " active"; ?> " id="login">
			  	<form id='f_login' action='/login' method='post'>
				  	<div class="control-group info">
					  <div class="control-label" for="login_id">
					  	用户名
					  	<span class='login_warn'>
					  		<?php
					  			if (isset($error)) 
					  				echo $error;
					  		?>
					  	</span>
					  </div>
					  <div class="controls">
					    <input type="text" id="login_id" name='login_id'>
					    <span class="login_warn">
					    </span>
					  </div>
					</div>
					<div class="control-group info">
					  <div class="control-label" for="passwd">密码</div>
					  <div class="controls">
					    <input type="password" id="passwd" name='passwd'>
					    <span class="login_warn"></span>
					  </div>
					</div>
					<div class='black_a login_line'>

						<input type='submit' class='btn btn-primary'value='登录'></span>
						<label class='black remember_me'>
							<input type='checkbox' class='r_check'name='stay_login' checked/>记住我
						</label>
						<!-- <a class='forget'href='/login/forget'>忘记密码</a> -->
					
					</div>
				</form>
			  </div>
			  <div class="tab-pane
			  <?php
			  		if ($register)
			  			echo " active"; ?> " id="register">
			  	<form id='f_register' action='/login/register' method='post'>
				  	<div class="control-group info">
					  <div class="control-label" for="reg_login_id">用户名</div>
					  <div class="controls">
					    <input type="text" id="reg_login_id" name='reg_login_id'>
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
					    <input type="password" id="reg_passwd" name='reg_passwd' datatype="*6-16"nullmsg="请输入密码"errormsg="密码范围在6~16位之间！">
					    <span class="login_warn"></span>
					  </div>
					</div>
					<div class="control-group info">
					  <div class="control-label" for="reg_passwd_con" datatype="*"recheck='reg_passwd'>密码确认</div>
					  <div class="controls">
					    <input type="password" id="reg_passwd_con" name='reg_passwd_con'>
					    <span class="login_warn"></span>
					  </div>
					</div>
					
					<div>
						<input type='submit' class='btn btn-primary'value='注册'>
					</div>
				</form>
			  </div>
			</div>
		</div>
		<script type="text/javascript">

		var iTime;
		var cl;
		function remainTime()
		{
		    if(iTime == 0)
		        clearTimeout(cl);
		    else
		        cl = setTimeout("remainTime()", 1000);
		    $('.t-left-n').text(iTime--);
		}
		function switch_tel_state() {
			clearTimeout(cl);
			$('.r-tel').removeClass('info');
			$('.r-tel').addClass('error');
			$('.e-tel').text('手机号等错误');
			$('.e-tel').removeClass('hide');
		}
		function switch_tel() {
			$('.r-tel').removeClass('error');
			$('.r-tel').addClass('info');
			$('.e-tel').text('');
			// $('.e-tel').addClass('hide');
		}
		$('#send').click(function() {
			var nu = $('#reg_login_id').val();
			if (nu != "" && !isNaN(nu) && nu.length == 11) {
				$.post("/sms",
			            {
			            	phone: function(){return $('#reg_login_id').val();}
			            },
			            function(data, textStatus) {
			            	if (data.state == 0) {
			            		$('.time-left').removeClass('hide');
			            		$('.t-left-n').text("");
			            		iTime = 60;
		        				clearTimeout(cl);
			            		remainTime();
			            		switch_tel();
			            	}
			            	else 
			            		switch_tel_state();
			    		},
			    		"json");
			}
			else 
			    switch_tel_state();

		});
		function lang(key) { 
			mylang = {
				'ls_input_myb': '请输入您的用户名', 
				'ls_login_password': '请输入密码', 
				'ls_tel_length': '长度为{0}-{1}位', 
				'ls_password_length': '密码长度为{0}-{1}位之间', 
				'ls_login_password_rep':"请再次输入相同的密码",
				'ls_user_id': '用户名长度为{0}-{1}位之间', 
				'ls_input_captcha': '请输入手机验证码', 
				'ls_tel_rep_digits': '请输入数字',
				'ls_tel_rep_length': '验证码长度为{0}位',
				'ls_email':'邮箱不能为空'
			}; 
			return mylang[key]; 
		}

		$("#f_register").validate(
		{
		rules: 
		{ 
			reg_login_id: { 
				required: true, 
				rangelength:[6,30],
				remote: { 
					url: "/login/check_userid", 
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
			},
			reg_user_id: {
				rangelength: [6, 30],
				remote: { 
					url: "/login/check_userid", 
					type: "post", 
					dataType: "json",
					contentType: "application/x-www-form-urlencoded; charset=utf-8"
				}
			},
			tel_con: {
				required:true,
				digits:true,
				rangelength:[4,4]
			},
			reg_email: {
				required:true,
				email: true,
				remote: { 
					url: "/login/check_email", 
					type: "post", 
					dataType: "json",
					contentType: "application/x-www-form-urlencoded; charset=utf-8"
				}
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
			reg_user_id: {
				rangelength: $.format(lang('ls_user_id')),
				remote: "该用户名已经被注册"
			},
			tel_con: {
				required:lang('ls_input_captcha'),
				digits: lang('ls_tel_rep_digits'),
				rangelength: $.format(lang('ls_tel_rep_length'))
			},
			reg_email: {
				required:lang('ls_email'),
				email: "不是有效的邮箱",
				remote: "邮箱已经被注册"
			}

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

		</script>
	</div>
</div>