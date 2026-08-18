<?php
include 'auth.php';

$mysqli = database();
$user = authenticated_user(extract_from_request('name'), extract_from_request('pass'), extract_from_request('legacy_pass'), $mysqli);
$data = extract_from_request('data');
$success = FALSE;
if ($user) {
  $stmt = $mysqli->prepare("UPDATE users SET content=? WHERE name=?");
  $stmt->bind_param('ss', $data, $user['name']);
  $stmt->execute();
  $success = $stmt->affected_rows >= 0;
}
$mysqli->close();
echo json_encode(array('success' => $success));
?>
