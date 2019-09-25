<!DOCTYPE html>
<html>
<head>
	<title>Редактировать запись</title>
</head>
<body>
	<h1>login page</h1>
		<div class="error_code">
		<?php 
			Helper::errorMessage();
		?>
	<form action="edit" method="POST" >
		<input type="hidden" name="id" value="<?= $taskToEdit['id'] ?>">
		<input type="text" name="user_name" placeholder="Имя пользователя" value="<?= $taskToEdit['user_name'] ?>">
		<input type="text" name="user_email" placeholder="E-mail пользователя" value="<?= $taskToEdit['user_email'] ?>">
		<input type="text" name="text" placeholder="Текст задачи" value="<?= $taskToEdit['text'] ?>">
		<p id="label"><input type="checkbox" name="completed">Отметить выполненным</p>
		<input type="submit" name="submit" value="Изменить задачу">
	</form>

	<a href="/beejee/">На главную</a>
</body>
</html>