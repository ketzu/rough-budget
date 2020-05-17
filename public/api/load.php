<?php
include 'conf.php';

$payload = "{}";

if($auth == TRUE)
{
  // Prepare Statement
  $stmt = $mysqli->prepare("SELECT content FROM users WHERE name=?");
  $stmt->bind_param('s',$name);
  $stmt->execute();
  $stmt->bind_result($payload);

  // Fetch result
  $res = $stmt->fetch();
  if($res == TRUE)
  {
    $success = TRUE;
  }else{
    $payload = "{}";
  }
}

$mysqli->close();
echo json_encode(array('data' => $payload, 'success' => $success));

?>