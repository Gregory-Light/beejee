<?php






class DB 
{

	//для подключения к БД
	const LOGIN = 'beejee';
	const PASSWORD = 'i8Rq6ZldOan7dV2D';


	static private $tasksPdo = false;



//исходное подключение к БД. Вызывается в начале каждого метода
	static private function connectToDb() {
		if (self::$tasksPdo === false) {
			self::$tasksPdo = new PDO('mysql:host=localhost;port=8889;dbname=beejee', self::LOGIN, self::PASSWORD);
			self::$tasksPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		$dbResult = self::$tasksPdo->prepare("SET NAMES utf8");
		$dbResult->execute();
		return ;
	}



//забрать все задачи из БД с сортировкой по имени таблицы
	static public function getAllTasks($sortBy) {
		self::connectToDb();
// ЗДЕСЬ ДЫРА, которую не смог придумать, как закрыть(mysql_escape_string в PHP7 отсутствует, а сам PDO не сортирует, если использовать подготовленные выражения)
		$pdoStatement = self::$tasksPdo->prepare("SELECT * from Tasks ORDER BY $sortBy");
		$pdoStatement->execute(array());
		return $pdoStatement->fetchall(PDO::FETCH_ASSOC);
	}


//сохранить задачу в БД
	static public function storeTask($user_name,$user_email,$text) {
		self::connectToDb();
		$pdoStatement = self::$tasksPdo->prepare("INSERT INTO Tasks (`user_name`, `user_email`, `text`) VALUES (:user_name, :user_email, :text)");
		$pdoStatement->execute(array(
			':user_name' => $user_name,
			':user_email' => $user_email,
			':text' => $text
		));
		return "added";
	}


//посчитать количество задач в БД(для расчета пагинации)
	static public function countTasks() {
		self::connectToDb();
		$pdoStatement = self::$tasksPdo->prepare("SELECT COUNT('id') FROM Tasks");
		$pdoStatement->execute(array());
		return $pdoStatement->fetchall(PDO::FETCH_NUM)[0][0];
	}

//загрузить конкретную задачу
	static public function loadTask($id) {
		self::connectToDb();
		$pdoStatement = self::$tasksPdo->prepare("SELECT * FROM Tasks WHERE id = :id");
		$pdoStatement->execute(array(
			':id' => "$id"));
		return $pdoStatement->fetch(PDO::FETCH_ASSOC);
	}


// внести изменения в задачу в БД
	static public function updateTask($id,$user_name,$user_email,$text,$completed) {
		self::connectToDb();
		$pdoStatement = self::$tasksPdo->prepare("UPDATE Tasks SET user_name = :user_name, user_email = :user_email, text = :text, edited = true, completed = :completed WHERE id = :id; ");
		$pdoStatement->execute(array(
			':user_name' => "$user_name",
			':user_email' => "$user_email",
			':text' => "$text",
			':id' => "$id",
			':completed' => "$completed",
		));
		return ;
	}

}




