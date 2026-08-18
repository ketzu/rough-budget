<?php
include 'auth.php';

$name = extract_from_request('name');
$registration = extract_from_request('registration');
$mysqli = database();
$user = find_user($name, $mysqli);
$salt = $registration ? base64_encode(random_bytes(16)) : ($user['kdf_salt'] ?? '');
if (!$registration && $user && $salt === '' && (int)$user['protocol_version'] === 1) {
  $salt = base64_encode(random_bytes(16));
  $stmt = $mysqli->prepare("UPDATE users SET kdf_salt=? WHERE name=?");
  $stmt->bind_param('ss', $salt, $user['name']);
  $stmt->execute();
}
if ($salt === '') $salt = dummy_salt($name);

// The response shape and status are identical for known and unknown names.
// However, it is clear that the requests take different amounts of time to process
// for a found user and not found user. I don't think it is THAT worth it to fix.
$mysqli->close();
echo json_encode(array(
  'salt' => $salt
));
?>
