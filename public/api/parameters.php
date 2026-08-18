<?php
include 'auth.php';

$name = extract_from_request('name');
$mysqli = database();
$user = find_user($name, $mysqli);
$salt = $user['kdf_salt'] ?: dummy_salt($name);
$version = 2;

// The response shape and status are identical for known and unknown names.
// However, it is clear that the requests take different amounts of time to process
// for a found user and not found user. I don't think it is THAT worth it to fix.
$mysqli->close();
echo json_encode(array(
  'protocolVersion' => $version,
  'salt' => $salt
));
?>
