<div class='m_right'>
	<div>
		<span>已有的"大学"|"网吧"：</span>
		<p class='red'>大学和网吧的概念是一样的，都是一个区域下的单位</p>
		<ul>
			<?php
			foreach ($univ_type as $type) { ?>
				<li><?=$type['univ_full_name']?></li>
			<?php } ?>
		</ul>
	</div>
	<div class='new_univ'>
		<form action='/rooter/new_univ' method='post' enctype="multipart/form-data">
			<label>添加新大学</label>
			<div>
				大学全名
				<input type='text' name='univ_full_name'/>
			</div>
			<div>
				大学首字母缩写，如宁波大学则为nbdx
				<input type='text' name='univ_short_name'/>
			</div>
			<div>
				大学图片
				<input type='file' name='univ_img'/>
				<!-- <input type='file' name='store_img' id='store_img'/> -->
			</div>
			<input type='submit' value='提交'/>
		</form>
	</div>
	<script type='text/javascript'>
		<?php
			if (@$info) { ?>
				alert('<?=$info?>');
		<?php
		} ?>
	</script>
</div>
</div>
</div>