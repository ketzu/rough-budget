<?php

########## Database Info ##########
$db_server = $_ENV["DB_SERVER"]?: '';
$db_name = $_ENV["DB_NAME"]?: '';
$db_user = $_ENV["DB_USER"]?: '';
$db_passwd = $_ENV["DB_PASSWORD"]?: '';

if($db_server == '')
	error_log("DB_SERVER not set.");
if($db_name == '')
	error_log("DB_NAME not set.");
if($db_user == '')
	error_log("DB_USER not set.");
if($db_passwd == '')
	error_log("DB_PASSWORD not set.");
