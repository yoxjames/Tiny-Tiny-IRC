#!/usr/bin/php
<?php
	require_once "config.php";
	require_once "functions.php";
	require_once "db.php";
	require_once "connection.php";

	$link = db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	init_connection($link);

	$connection_id = db_escape_string($argv[1]);

	$result = db_query($link, "SELECT * FROM ttirc_connections
		WHERE id = $connection_id");

	if (db_num_rows($result) == 1) {

		$line = db_fetch_assoc($result);

		_debug("[$connection_id] connecting to server " . $line["server"]);
	
		$connection = new Connection($link, $connection_id, $line["encoding"], 
			$line["last_sent_id"]);
		$connection->setDebug(false);
		$connection->setUser($line["ident"], $line['nick'], 
			$line['realname'], '+i');
		$connection->setServer($line["server"], $line["port"]);
	
		if ($connection->connect()) {
			_debug("[$connection_id] connection established.");
			$connection->run();
		}
	}

	db_close($link);
?>
