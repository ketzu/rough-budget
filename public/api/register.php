<?php
include 'auth.php';

$name = extract_from_request('name');
$pass = extract_from_request('pass');
$mysqli = database();
$user = find_user($name, $mysqli);
$success = TRUE;

if (!$user && $name !== '' && $pass !== '' && valid_salt(extract_from_request('kdf_salt'))) {
  $salt = extract_from_request('kdf_salt');
  $verifier = password_hash($pass, PASSWORD_DEFAULT);
  $stmt = $mysqli->prepare("INSERT INTO users (name, password, protocol_version, kdf_salt, content) VALUES (?, ?, 2, ?, '')");
  if ($stmt === FALSE) {
    http_response_code(500);
    $success = FALSE;
  } else {
    $stmt->bind_param('sss', $name, $verifier, $salt);
    $success = $stmt->execute();
    if (!$success) {
      http_response_code(500);
    }
  }
} else {
  http_response_code(400);
  password_hash($pass, PASSWORD_DEFAULT);
  $success = FALSE;
}

// Do not confirm whether a username was already registered.
$mysqli->close();
echo json_encode(array('success' => $success));
?>