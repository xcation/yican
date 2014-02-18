<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <script type='text/javascript'src='/js/jquery.1.8.0.min.js'></script>
  <script src="/js/bootstrap.js"></script>
</head>
<body>
	<form action="/chf/web_register" method='post'>
		<div>
			<label>choose an id</label>
			<input type='text' name='userid' />
		</div>
		<div>
			<label>password</label>
			<input type='password' name='passwd' />
		</div>
		<div>
			<label>confirm your password again</label>
			<input type='password' name='passwd_again' />
		</div>
		<input type='submit' value='submit' />
	</form>
</body>