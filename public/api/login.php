<?php
include 'auth.php';

$name = extract_from_request('name');
$pass = extract_from_request('pass');
$legacy_pass = extract_from_request('legacy_pass');
$mysqli = database();
$user = find_user($name, $mysqli);
$success = verify_authentication($user, $pass, $legacy_pass);
$session = NULL;

if ($success && $user) {
  $session = create_session($user['name'], $mysqli);
  if ((int)$user['protocol_version'] === 1) {
    $newVerifier = password_hash($pass, PASSWORD_DEFAULT);
    $salt = extract_from_request('kdf_salt');
    if (valid_salt($salt)) {
      $stmt = $mysqli->prepare("UPDATE users SET auth_verifier=?, protocol_version=2, kdf_salt=? WHERE name=?");
      $stmt->bind_param('sss', $newVerifier, $salt, $user['name']);
      $stmt->execute();
    }
  }
}

$mysqli->close();
echo json_encode(array('success' => $success && $user !== NULL, 'session' => $session));
?>
