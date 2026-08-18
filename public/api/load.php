<?php
include 'auth.php';

$mysqli = database();
$session = session_user(extract_from_request('session'), $mysqli);
$payload = '{}';
$success = FALSE;
if ($session) {
  $stmt = $mysqli->prepare("SELECT content FROM users WHERE name=?");
  $stmt->bind_param('s', $session['name']);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if ($result) {
    $payload = $result['content'] ?: '{}';
    $success = TRUE;
  }
}
$mysqli->close();
echo json_encode(array('data' => $payload, 'success' => $success));
?>
