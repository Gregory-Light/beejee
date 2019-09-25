<?php 

//Экранировать данные
$enteredName = htmlentities($_POST['user_name']);
$enteredEmail = htmlentities($_POST['user_email']);
$enteredText = htmlentities($_POST['text']);


//Валидировать данные
if (!($enteredName AND $enteredEmail AND $enteredText)) {
   echo "1";
   $_SESSION['errorCode'] = 'Требуется заполнить все поля!';
   header('Location:/beejee');
   exit;
}

if (!filter_var($enteredEmail, FILTER_VALIDATE_EMAIL)) {          
   echo "2";                              
   $_SESSION['errorCode'] = 'Некорректно введен e-mail';
   header('Location:/beejee');
   exit;
}

//Записать в БД, если все верно
DB::storeTask($enteredName,$enteredEmail,$enteredText);
$_SESSION['notification'] = "Задача добавлена";
header('Location:/beejee');
exit;




?>