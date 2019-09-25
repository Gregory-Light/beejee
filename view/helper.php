<?php  class Helper {
	public static function errorMessage() {
		if (isset($_SESSION['errorCode'])) {
			echo $_SESSION['errorCode'];
			unset($_SESSION['errorCode']);
		}

	}

	public static function notificationMessage() {
		if (isset($_SESSION['notification'])) {
			echo $_SESSION['notification'];
			unset($_SESSION['notification']);
		}

	}
}

?>