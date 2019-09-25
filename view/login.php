<!DOCTYPE html>
<html>
<head>
	<title>login page</title>
</head>
<body>
	<h1>login page</h1>
		<div class="error_code">
		<?php 
		if (isset($_SESSION['errorCode'])) {
			echo $_SESSION['errorCode'];
			unset($_SESSION['errorCode']);
		}
		?>
	<form method="POST" action="login">
		<input type="text" name="login" placeholder="Имя пользователя">
		<input type="password" name="password" placeholder="Пароль">
		<input type="submit" name="submit" value="Войти">
	</form>

	<a href="beejee">На главную</a>
</body>
</html>