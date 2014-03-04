<script src="<?=constant("mycycbase")?>/js/jquery-1.10.2.min.js"></script>
<script src="<?=constant("mycycbase")?>/js/bootstrap.js"></script>
<?php
if (@$return_university_id && @$return_store_id) { ?>
	<script type="text/javascript">
		alert('您的篮子不为空哦～');
		window.location.href="<?=constant('mycycbase')?>/store/<?=$return_university_id?>/<?=$return_store_id?>";
	</script>
<?php
} 
else { ?>
<div id="cart-header">
美食篮子--回到<a href="<?=constant('mycycbase')?>/store/<?=$university_id?>/<?=$store_id?>"><?=$store_name?></a>
</div>
<form action="<?=constant('mycycbase')?>/cart" method="post">
	<table id="order-item-table">
		<thead>
			<tr>
				<td class="name">食物</td>
				<td class="quantity">数量</td>
				<td class="subtotal">小计</td>
				<td class="action">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
			$total_price=0;
			foreach($order as $item){
		?>
				<tr>
					<td class="name"><?=$item['foodName']?></td>
					<input type='hidden' value='<?=$item['foodName']?>' name='food_name[]'/> 
					<input type='hidden' value='<?=$item['foodPrice']?>' name='food_price[]'/> 
					<td class="quantity"><input type="text" class="form-control input-sm" value="<?=$item['amount']?>" name="amount[<?=$item['foodId']?>]"/></td>
					<td class="subtotal"><?=$item['amount']*$item['foodPrice']?></td>
					<td class="action"><a href="<?=constant('mycycbase')?>/cart/delete/<?=$item['foodId']?>">删除</a></td>
				</tr>			
		<?php
				$total_price+=$item['amount']*$item['foodPrice'];
			}
		?>
			<tr>
				<td class="name">总计</td>
				<td class="quantity"><input type="submit" value="更新数量"></input></td>
				<td class="subtotal"><?=$total_price?></td>
				<td class="action"><a href="<?=constant('mycycbase')?>/deletecookie2?rUrl=<?=constant('mycycbase')?>/store/<?=$university_id?>/<?=$store_id?>">清空</a></td>
			</tr>
		</tbody>
	</table>
</form>
<?php
	if($total_price < $delivery_cost){ ?>
		<div id="not-enough">
			对不起，您订的食物总价还没有达到起送价<?=$delivery_cost?>元。还不能提交，继续点餐吧
		</div>
<?php
}
else { ?>
<form action="<?=constant('mycycbase')?>/cart/check" method="post" id="address-form">
	<?php
	foreach($order as $item){ ?>
		<input type='hidden' value='<?=$item['foodName']?>' name='food_name[]'/> 
		<input type='hidden' value='<?=$item['foodPrice']?>' name='food_price[]'/> 
		<input type="hidden" value="<?=$item['amount']?>" name="amount[<?=$item['foodId']?>]"/>
	<?php
	} ?>
	<div id="address-header">确认送餐信息：</div>
	<div id="address-box">
		<div class='addr_list'>
		<?php
			$i = 0;
			if (@$all_address && count($all_address) > 0) { ?>
				<div>
					<a class="btn btn-success" id="new_addr" href="<?=constant('mycycbase')?>/cart/new_addr">新建一个地址</a>
				</div>
				<input type='hidden' value='1' name='has_default_address'/>
				<?php
				foreach ($all_address as $row) { ?>
					<div class='addr_item' pos="<?=$row['id']?>">
						<input type='radio' name='choosen_address' <?php if ($i++ == 0) echo "checked"?> value="<?=$row['id']?>" />
						<span class="addr_detail">
							地址：<span class="user_pos"><?=$row['userPos']?></span>; 
							电话：<span class="main_phone"><?=$row['userPhone_main']?></span>; 
							短号：<span class="short_phone"><?=$row['userPhone_short']?></span>
							<a class="blue modify_addr" href="<?=constant('mycycbase')?>/cart/modify_addr/<?=$row['id']?>" />修改</a>&nbsp;<a class="blue delete_addr" href="<?=constant('mycycbase')?>/cart/delete_addr/<?=$row['id']?>">删除</a>
						</span>
					</div>
				<?php
				} ?>
			<?php
			}
			else { ?>
				<input type='hidden' value='0' name='has_default_address'/>
				<table id="address-table">
					<tr>
						<td class="des">地址(必填):</td>
						<td class="info"><input type="text" class="form-control" maxlength="100" name="info[address]" id="address"/></td>
					</tr>
					<tr>
						<td class="des">长号(必填):</td>
						<td class="info"><input type="text" class="form-control" maxlength="11" name="info[mobile]" id="mobile"/></td>
					</tr>
					<tr>
						<td class="des">短号:</td>
						<td class="info"><input type="text" class="form-control" maxlength="6" name="info[short_mobile]" /></td>
					</tr>
				</table>
			<?php
			} ?>
		</div>
		<table>
			<tr>
				<td class="des">备注:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="info"><input type="text" maxlength="200" class="form-control"name="info[ps]" /></td>
			</tr>
		</table>
	</div>
	<input type="hidden" value="<?=$university_id?>" name="info[university_id]" />
	<input type="hidden" value="<?=$store_id?>" name="info[store_id]" />
	<div id="submit-button-wrapper">
		<input class="btn btn-default" id="submit-button" type='submit' value='提交'/>
	</div>
</form>
<?php
} ?>
<?php
} ?>