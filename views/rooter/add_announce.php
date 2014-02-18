<div class='m_right'>
	<script charset="utf-8" src="/js/kindeditor/kindeditor.js"></script>
	<script charset="utf-8" src="/js/kindeditor/lang/zh_CN.js"></script>
	<script>
	        KindEditor.ready(function(K) {
	                window.editor = K.create('#editor_id');
	        });
	</script>
	<h3>修改公告</h3>
	<h5>已有的公告：</h5>
	<?php
	foreach($old_announce as $row) { ?>
		<div>
			<span>内容：<?=$row['announce_content']?></span>
			<span>时间：<?=$row['createTime']?></span>
			<span class='black_a'><a href='/rooter/add_announce/<?=$row['announce_id']?>'>删除该公告</a></span>
		</div>
	<?php 
	} ?>
	<h5>新增公告</h5>
	<form action='/rooter/add_announce' method='post'>
		<textarea id="editor_id" name="announce_content" style="width:700px;height:300px;"></textarea>
		<input type='submit' value='提交'/>
		<!-- <textarea colomn='20' row='10' style='height:100px'name='announce_content'></textarea>
		<input type='submit' value='提交'/> -->
	</form>
	<script type="text/javascript">
		<?php
		if (@$success) { ?>
			alert('提交成功');
		<?php
		} 
		if (@$delete) { ?>
			alert('删除成功');
		<?php
		} ?>
	</script>
</div>
</div>
</div>


