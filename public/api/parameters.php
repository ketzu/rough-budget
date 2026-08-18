<?php
include 'auth.php';

$name = extract_from_request('name');
$mysqli = database();
$user = find_user($name, $mysqli);
$salt = $user['kdf_salt'] ?: dummy_salt($name);
$version = 2;
$kdf = 'PBKDF2-SHA-256';
$iterations = 600000;

// The response shape and status are identical for known and unknown names.
$mysqli->close();
echo json_encode(array(
  'protocolVersion' => $version,
  'kdf' => $kdf,
  'iterations' => $iterations,
  'salt' => $salt
));
?>
