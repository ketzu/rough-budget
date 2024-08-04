<?php
include 'conf.php';

// Create connection
try {
	$mysqli = new mysqli($db_server, $db_user, $db_passwd);
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$db_name}`");
    $mysqli->close();
    $result['database'] = "success";
} catch (Exception $e) {
	error_log("Failed to create db: {$e->getMessage()}");
    $result['creation'] = "ERROR {$e->getMessage()}";
}

try {
	$mysqli = new mysqli($db_server, $db_user, $db_passwd, $db_name);
    $mysqli->query("
        CREATE TABLE IF NOT EXISTS `users` (
            name varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            lastaccessed timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            content mediumtext,
            PRIMARY KEY (name)
        )");
    $mysqli->close();
    $result['table'] = "success";
} catch (Exception $e) {
	error_log("Failed to create table: {$e->getMessage()}");
    $result['table'] = "ERROR {$e->getMessage()}";
}

echo json_encode($result);