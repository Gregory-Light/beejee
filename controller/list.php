<?php 
// Получить требования по сортировке и пагинации
$sortBy = 'id';
$page = 1;
$columns = array ('id','user_name','user_email','text','completed','edited');
foreach ($params_array as $param) {
	$arr = explode('=', $param);
	if (($arr[0] ==='sort_by') and (in_array($arr[1], $columns, true))) { $sortBy = $arr[1]; }
	if (($arr[0] ==='page') and (isset($arr[1]))) { $page = $arr[1]; }
}



// Загрузить все задачи из БД
$tasks = DB::getAllTasks($sortBy);

//Взять те, что принадлежат текущей странице
$tasksToShow = array_slice($tasks,($page-1)*3,3);
$taskCount = DB::countTasks();
$pagesCount = intdiv($taskCount,3)+1;
$userLoggedIn = lightUser::checkLogin()['result'];

// Передать  данные в вид
include 'view/list.php';





?>