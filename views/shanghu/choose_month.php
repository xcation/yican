<form action='/shanghu/sale_info/<?=$store_id?>' method='post'>
	<select name='sale_month'>
		<option value='0' selected>这个月</option>
		<option value='1'>上个月</option>
		<option value='2'>两个月前</option>
	</select>
	<input type='submit' value='查询'class='btn'/>
</form>