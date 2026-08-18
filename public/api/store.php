<?php
include 'auth.php';

$mysqli = database();
$session = session_user(extract_from_request('session'), $mysqli);
$data = extract_from_request('data');
$success = FALSE;
if ($session) {
  $stmt = $mysqli->prepare("UPDATE users SET content=? WHERE name=?");
  $stmt->bind_param('ss', $data, $session['name']);
  $stmt->execute();
  $success = $stmt->affected_rows >= 0;
}
$mysqli->close();
echo json_encode(array('success' => $success));
?>
