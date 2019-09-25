<?php 
//Необходимые файлы подключаются здесь
include 'includes.php';
//массив под параметры запроса
$params_array = array();

//отделить путь от параметров
$url = $_SERVER['REQUEST_URI']; 
if (strpos($url,'?') > 0) {
	$path = explode('?', $url)[0];
	$params = explode('?', $url)[1];
	$params_array = explode('&',$params);
}
else
{
	$path = $url;
}

//массив маршрутов
$routes = array(
	'/beejee/' 	=> "controller/list.php",
	'/beejee/add' => "controller/add.php",
	'not_found' => "view/not_found.php",
	'/beejee/login' => "controller/login.php",
	'/beejee/logout' => "controller/logout.php",
	'/beejee/edit' => "controller/edit.php",
);

// Направить по маршруту, если такового нет - Not found
if (isset($routes[$path])) {
include($routes[$path]);
}
else
	include($routes['not_found']);
?>