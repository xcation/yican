<h3>用户登录</h3>
<div>
	<form method="post">
		<table>
			<tr>
				<td>用户名：</td>
				<td><input type="text" class="form-control" name="user" /></td>
			</tr>
			<tr>
				<td>密码：</td>
				<td><input type="password" class="form-control" name="passwd" /></td>
			</tr>
			<tr>
				<td colspan="2"><?=$hint?></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="btn btn-default" value='登录'/></td>
			</tr>
		</table>
	</form>
</div>