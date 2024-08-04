<?php
include 'auth.php';

$pw = "";
if($exists == FALSE) {
  $data = "";
  $stmt = $mysqli->prepare("INSERT INTO users (name, password, content) VALUES (?,?,?)");
  $pw = password_hash($pass, PASSWORD_DEFAULT);
  $stmt->bind_param('sss',$name, $pw, $data);
  $stmt->execute();
  $success = TRUE;
}

$mysqli->close();
echo json_encode(array('success' => $success));

?>