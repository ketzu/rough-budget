<?php
header('Content-type: application/json');

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

########## READ DATA

$name = $_POST["name"]?: '';
$pass = $_POST["pass"]?: '';
$data = $_POST["data"]?: '';

// Create connection
$mysqli = new mysqli($db_server, $db_user, $db_passwd, $db_name);
if ($mysqli->connect_errno) {
    die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
}

function auth($name, $pass, $mysqli)
{
  // auth
  $stmt = $mysqli->prepare("SELECT password FROM users WHERE name=?");
  $stmt->bind_param('s',$name);
  $stmt->execute();
  $stmt->bind_result($hash);
  
  // Fetch result
  $res = $stmt->fetch();
  $login = FALSE;
  if($res == TRUE)
  {
    if(password_verify($pass, $hash))
    {
      $login = TRUE;
    }
  }
  $stmt->free_result();
  return array($login,$res);
}

$try = auth($name, $pass, $mysqli);

$auth = $try[0];
$exists = $try[1];

$success = FALSE;
?>