<?php 
// Если не был отправлен POST, выполняем переход на форму входа
if (lightUser::checkLogin()['result']) {
	$_SESSION['errorCode'] = 'Пользователь уже авторизован!';
	header('Location:/beejee');
	exit;
}
if (!isset($_POST['submit'])) {
	include('view/login.php');
}

else


{
//иначе - проверяем ввод
	$enteredLogin = htmlentities($_POST['login']);
	$enteredPassword = htmlentities($_POST['password']);
	if (!($enteredLogin AND $enteredPassword)) {
		$_SESSION['errorCode'] = 'Требуется заполнить все поля!';
		header('Location:/beejee/login');
		exit;
	}



	$loginResult = lightUser::loginUser($enteredLogin,$enteredPassword);
	if ($loginResult['result']) {
		header('Location:/beejee/');
		exit;
	}
	else
	{
		$_SESSION['errorCode'] = 'Не удалось авторизовать пользователя!';
		header('Location:/beejee/login');
		exit;
	}
}
?>