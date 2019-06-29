<?php
	if (!isset($nodirect)) die('nope');

	session_start();

	include_once 'config.php';

	$db['con'] = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST.';port='.DB_PORT, DB_USER, DB_PASS, DB_OPTIONS);


	if (!isset($_SESSION['POS']['loggedin'])) {
			$_SESSION['POS']['loggedin'] = false;
	}

	function out($txt) {
			return htmlspecialchars($txt);
	}

	function nlTo($txt, $replace) {
		return preg_replace('/(\r\n)|\r|\n/', $replace, $txt);
	}


?>
