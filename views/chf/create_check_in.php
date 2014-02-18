<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <script type='text/javascript'src='/js/jquery.1.8.0.min.js'></script>
  <script src="/js/bootstrap.js"></script>
</head>
<body>
	<form action="/chf/web_create_check_in" method='post'>
		<p>
			<label>time limitation, measured by second, like 30</label>
		</p>
			<input type='text' name="expired_time" />
		<p>
			<label>random code for check in, four bit!</label>
		</p>
			<input type='text' name='random_code' />
		<p>
			<input type='submit' value='submit'/>
		</p>
	</form>
</body>