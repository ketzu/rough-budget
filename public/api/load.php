<?php
include 'auth.php';

$mysqli = database();
$user = authenticated_user(extract_from_request('name'), extract_from_request('pass'), extract_from_request('legacy_pass'), $mysqli);
$payload = '{}';
$success = FALSE;
if ($user) {
  $stmt = $mysqli->prepare("SELECT content FROM users WHERE name=?");
  $stmt->bind_param('s', $user['name']);
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
