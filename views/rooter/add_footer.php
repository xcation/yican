<div class='m_right'>
	<script charset="utf-8" src="/js/kindeditor/kindeditor.js"></script>
	<script charset="utf-8" src="/js/kindeditor/lang/zh_CN.js"></script>
	<script>
	        KindEditor.ready(function(K) {
	                window.editor = K.create('#editor_id');
	        });
	</script>
	<h5>生成的链接是：<?=@$href?></h5>
	<h5>已有的尾部：</h5>
	<?php
	if (@$content) {
		foreach($content as $row) { ?>
			<div>
				<a class='blue'href="<?=$row['link_href']?>"><?=$row['label_name']?></a>
				<span class='black_a'><a href='/rooter/add_footer/<?=$row['orders']?>'>删除该链接</a></span>
			</div>
		<?php
		}
	}
	else {
		echo "暂无尾部";
	} ?>
	<h5>新增页面内容</h5>
	<form action='/rooter/add_footer' method='post' id="my_form">
		<textarea id="editor_id" name="footer_content" style="width:700px;height:300px;"></textarea>
		<input type='submit' id='text_submit'value='提交'/>
		<!-- <textarea colomn='20' row='10' style='height:100px'name='announce_content'></textarea>
		<input type='submit' value='提交'/> -->
	</form>
	<h5>新增尾部链接</h5>
	<form action='/rooter/add_footer' method='post'>
		显示的链接名字:<input type='text' name='footer_lable_name_zh'/>
		链接地址:<input type='text' name='href'/>
		<input type='submit' value='提交'/>
	</form>
	<script type="text/javascript">
		<?php
		if (@$href) { ?>
			alert("生成的链接是<?=$href?>, 在顶部查看");
		<?php
		}
		if (@$insert_href) { ?>
			alert("插入尾部链接成功");
		<?php
		}
		if (@$deleted) { ?>
			alert('删除成功');
		<?php
		} ?>
		// $("#text_submit").click(function() {
		// 	$("#real_content").val($('.ke-content').text());
		// 	$("#my_form").submit();
		// });
	</script>
</div>
</div>
</div>

