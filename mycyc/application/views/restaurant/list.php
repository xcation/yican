<div class="address">	
	<?php 
		$cookie=array(
			'name' => 'xiaoqu',
			'value' => $pageName,
			'expire' => '3600',
			'prefix' => 'mycyc_'
		);
		$this->input->set_cookie($cookie);
		echo $college;
	?>
	<a href=<?=constant("mycycbase")."/deletecookie"?>>更换地址</a>
	<form action=<?=constant("mycycbase").'/restaurant/'.$pageName.'/'?> method="get" id="filter">
		<select name="taste" id="taste_choice">
			<option value="0">全部口味</option>
			<?php $count = 0; ?>
			<?php foreach($store_type as $item):?>
			<option value="<?=++$count?>" <?php if($taste==$count)echo "selected";?>><?=$item['storeTypeName']?></option>
			<?php endforeach;?>
		</select>
	</form>
	<div>
		<?php
		foreach ($block_info as $one_block) { ?>
		<a href="<?=constant('mycycbase')?>/restaurant/<?=$short_name?>/<?=$one_block['block_num']?>"><?=$one_block['block_name']?></a>
		<?php
		} ?>
	</div>
</div>

<div class="restaurant">
	<?php require("restaurant_body.php");?>
</div>

<script src="<?=constant("mycycbase")?>/js/jquery-1.10.2.min.js"></script>
<script>
	$("#taste_choice").change(function(){
		$("#filter").submit();
	});  
	$("#opening").click(function() {
		if($('#opening').attr('checked'))
			$('#opening').removeAttr('checked');
		else
			$('#opening').attr('checked', true);
		$("#filter").submit();
	});
</script>