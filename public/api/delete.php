<?php
include 'auth.php';

$mysqli = database();
$session = session_user(extract_from_request('session'), $mysqli);
$success = FALSE;
if ($session) {
  $stmt = $mysqli->prepare("DELETE FROM users WHERE name=?");
  $stmt->bind_param('s', $session['name']);
  $stmt->execute();
  $success = TRUE;
}
$mysqli->close();
echo json_encode(array('success' => $success));
?>
