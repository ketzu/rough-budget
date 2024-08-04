<?php
header('Content-type: application/json');

include 'conf.php';

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