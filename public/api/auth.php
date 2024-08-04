<?php
header('Content-type: application/json');
ini_set('display_errors',0);

include 'conf.php';

########## READ DATA

$name = $_POST["name"]?: '';
$pass = $_POST["pass"]?: '';
$data = $_POST["data"]?: '';

// Create connection
try {
	$mysqli = new mysqli($db_server, $db_user, $db_passwd, $db_name);
} catch (Exception $e) {
	error_log("Connection failed: {$e->getMessage()}");
	die(json_encode("database connection failed"));
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