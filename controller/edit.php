<?php 

//Пользователь авторизован?
$userLoggedIn = lightUser::checkLogin();
if (!$userLoggedIn['result']) {
	$_SESSION['errorCode'] = 'Пользователь не авторизован!';
	header('Location:/beejee');
	exit;
}

//Если мы еще не отправили данные на редактирование, проверим, корректно ли передан id
if(!isset($_POST['submit'])) {
	if (isset($_GET['id']) and (is_numeric($_GET['id']))) {
		$id = htmlentities($_GET['id']);
	}
	else
	{
		$_SESSION['errorCode'] = 'некорректный id!';
		header('Location:/beejee');
		exit;
	}
//Подгрузим задачи
	$taskToEdit = DB::loadTask($id);
	if (!$taskToEdit) {
		$_SESSION['errorCode'] = 'Не удалось загрузить запись из БД';
		header('Location:/beejee');
		exit;
	}
//рендерим форму редактирования
	include('view/edit.php');
// Загрузить запись и вывести ее в вид
}

//если форма отправлена
else
{
//экранируем
	$enteredid = htmlentities($_POST['id']);
	$enteredName = htmlentities($_POST['user_name']);
	$enteredEmail = htmlentities($_POST['user_email']);
	$enteredText = htmlentities($_POST['text']);
	$completed = isset($_POST['completed']);	

//валидируем
	if (!($enteredName AND $enteredEmail AND $enteredText)) {
		$_SESSION['errorCode'] = 'Требуется заполнить все поля!';
		header('Location:/beejee/edit?id='. $enteredid);
		exit;
	}

	if (!filter_var($enteredEmail, FILTER_VALIDATE_EMAIL)) {          
		$_SESSION['errorCode'] = 'Некорректно введен e-mail';
		header('Location:/beejee/edit?id='. $enteredid);
		exit;
	}

//сохраняем запись в бд
DB::updateTask($enteredid,$enteredName,$enteredEmail,$enteredText,$completed);
$_SESSION['notification'] = "Задача изменена";
		header('Location:/beejee/');
		exit;

}



?>