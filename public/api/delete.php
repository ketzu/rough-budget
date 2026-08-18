<?php
include 'auth.php';

$mysqli = database();
$user = authenticated_user(extract_from_request('name'), extract_from_request('pass'), extract_from_request('legacy_pass'), $mysqli);
$success = FALSE;
if ($user) {
  $stmt = $mysqli->prepare("DELETE FROM users WHERE name=?");
  $stmt->bind_param('s', $user['name']);
  $stmt->execute();
  $success = TRUE;
}
$mysqli->close();
echo json_encode(array('success' => $success));
?>
