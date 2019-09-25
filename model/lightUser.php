<?php

/**
 * Description of lightUser
 *
 * @author Light
 */
class lightUser {

    static private $pdo = false;

    const TIMEOUT = 600;
    const COOKIE_EXPIRE_TIME = 2592000;
    const LOGIN = 'beejee';
    const PASSWORD = 'i8Rq6ZldOan7dV2D';

    private static function generateHash() {
        $chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLenght = strlen($chars);
        $randomString = '';
        $randomLenght = rand(5, 30);
        for ($i = 0; $i < $randomLenght; $i++) {
            $randomString .= $chars[rand(0, $charsLenght - 1)];
        }

        return sha1($randomString);
    }

    public static function createUser($login, $password, $userName) {
        self::connectToDB();
        //        Существует ли такой пользователь?
        if (self::loginExists($login)) {
            return "Login is in use!";
        }
        //Сгенерировать соль
        $salt = self::generateHash();
        $saltedPassword = hash('sha512', $password . $salt);
        echo "salt = $salt <br>";
        echo 'Salted password - ' . $saltedPassword . "<br>";

//        Создать запись в БД
        self::connectToDB();
        $pdoStatement = self::$pdo->prepare('INSERT INTO `Users` (`login`, `passwordHash`, `salt`, `isadmin`) VALUES (:login, :passwordHash, :salt, 1)');
        $pdoStatement->execute(array(
            ':login' => $login,
            ':passwordHash' => $saltedPassword,
            ':salt' => $salt
        ));
        return "Added user with ID $userId";
    }


//Принимает логин и пароль в чистом виде
//Возвращает массив, ключ result - результат попытки
    public static function loginUser($login, $password) {

        $returnResult['result'] = false;
        $returnResult['description'] = 'Login failed';
        $returnResult['params'] = [];

        if (self::checkLogin()['result']) {
            $returnResult['result'] = false;
            $returnResult['description'] = 'User already authorized. Please logout first';
        }

        self::connectToDB();
        if (self::loginExists($login)) {
            $pdoStatement = self::$pdo->prepare('SELECT user_id,login,passwordHash,salt,full_name FROM Users WHERE login = :login LIMIT 1');
            $pdoStatement->execute(array(
                ':login' => $login
            ));
            $DBResult = $pdoStatement->fetch(PDO::FETCH_ASSOC);
            $salt = $DBResult['salt'];
            $enteredPasswordHash = hash('sha512', $password . $salt);
            if ($enteredPasswordHash === $DBResult['passwordHash']) {
                $cookieToken = self::generateHash();
                $loginTime = time();
                $pdoStatement = self::$pdo->prepare('INSERT INTO `Sessions` (`session_hash`, `user_id`, `login_time`, `expired`) VALUES (:sessionHash, :userId, :loginTime, FALSE)');
                $pdoStatement->execute(array(
                    ':sessionHash' => $cookieToken,
                    ':userId' => $DBResult['user_id'],
                    ':loginTime' => $loginTime
                ));

                setcookie('lightUser_SessionHash', $cookieToken,time()+ self::COOKIE_EXPIRE_TIME);
                $_COOKIE['lightUser_SessionHash'] = $cookieToken;

                $returnResult['result'] = true;
                $returnResult['description'] = "$login is authorized";
                $returnResult['params']['user_id'] = $DBResult['user_id'];
                $returnResult['params']['full_name'] = $DBResult['full_name'];
            }
        }
        return $returnResult;
    }

    //разлогинивает пользователя
    public static function logoutUser() {
        if (isset($_COOKIE['lightUser_SessionHash'])) {
            $sessionHash = $_COOKIE['lightUser_SessionHash'];
            setcookie("lightUser_SessionHash", "", time() - 3600);
            self::pdoExpireSession($sessionHash);
            return "logged out";
        } else {
            return "No session hash is set";
        }
    }

//    returns true or false
    public static function loginExists($login) {
        $returnResult = false;
        $pdoStatement = self::$pdo->prepare('SELECT user_id FROM Users WHERE login = :login');
        $pdoStatement->execute(array(
            ':login' => $login
        ));
        $result = $pdoStatement->fetchall(PDO::FETCH_ASSOC);
        if ($result) {
            $returnResult = true;
        }

        return $returnResult;
    }

//возвращает массив. Ключ result сообщает, залогинен ли  сейчас пользователь
    public static function checkLogin() {

        $returnResult['result'] = false;
        $returnResult['description'] = '';
        $returnResult['params'] = [];
        $currentTime = time();

        //Кука существует? Извлечь из БД запись о ней
        if (isset($_COOKIE['lightUser_SessionHash'])) {
            $sessionHash = $_COOKIE['lightUser_SessionHash'];
            $sessionRowFromDB = self::pdoGetSession($sessionHash);

            //Если строка не пустая
            if (isset($sessionRowFromDB['login_time'])) {
                $loginTime = $sessionRowFromDB['login_time'];
                if ($loginTime + self::TIMEOUT >= $currentTime) {
//                    Сессия не просрочена?
                    $returnResult['description'] = "User logged in";
                    $returnResult['params']['user_id'] = $sessionRowFromDB['user_id'];
                    $returnResult['params']['full_name'] = $sessionRowFromDB['full_name'];
                    $returnResult['result'] = true;
                    //TODO Увеличить lastlogin для сессии
                } else {
//                    Если сессия просочена, то кука не нужна
                    setcookie("lightUser_SessionHash", "", time() - 3600);
                    $returnResult['result'] = false;
                    $returnResult['description'] = 'Session timed out';
                    self::pdoExpireSession($sessionHash);
                    //Установить значение сессии expired
                }
            } else {
                //Если не нашлось записей в БД
                $returnResult['result'] = false;
                $returnResult['description'] = 'No session data in DB';
                setcookie("lightUser_SessionHash", "", time() - 3600);
            }
        } else {
            //Если кука не существует

            $returnResult['result'] = false;
            $returnResult['description'] = 'No session cookie';
        }
        return $returnResult;
    }

    private static function connectToDB() {
        if (self::$pdo === false) {
            self::$pdo = new PDO('mysql:host=localhost;port=8889;dbname=beejee', self::LOGIN, self::PASSWORD);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    private static function pdoGetSession($sessionHash) {
        self::connectToDB();
        $pdoStatement = self::$pdo->prepare('SELECT Sessions.user_id, Sessions.login_time, Users.full_name FROM Sessions JOIN Users ON Sessions.user_id = Users.user_id WHERE session_hash = :hash AND expired = FALSE LIMIT 1');
        $pdoStatement->execute(array(
            ':hash' => $sessionHash
        ));
        return $pdoStatement->fetch(PDO::FETCH_ASSOC);
    }

    private static function pdoExpireSession($sessionHash) {
        self::connectToDB();
        $pdoStatement = self::$pdo->prepare('UPDATE Sessions SET expired = 1 WHERE session_hash = :hash');
        $pdoStatement->execute(array(
            ':hash' => $sessionHash
        ));
    }

}
