<?php
header('Content-type: application/json');

########## Database Info ##########
$db_server = 'rdbms.strato.de';
$db_name = 'DB3117760';
$db_user = 'U3117760';
$db_passwd = 'bl3KfLR71qzhRsrI';

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