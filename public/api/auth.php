<?php
header('Content-type: application/json');
ini_set('display_errors', 0);

include 'conf.php';

function extract_from_request($name)
{
  static $json = NULL;
  if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== FALSE) {
    if ($json === NULL)
      $json = json_decode(file_get_contents('php://input'), TRUE) ?: array();
    return $json[$name] ?? '';
  }
  return $_POST[$name] ?? '';
}

function database()
{
  global $db_server, $db_user, $db_passwd, $db_name;
  mysqli_report(MYSQLI_REPORT_OFF);
  $mysqli = new mysqli($db_server, $db_user, $db_passwd, $db_name);
  if ($mysqli->connect_errno) {
    error_log("Database connection failed: {$mysqli->connect_error}");
    http_response_code(503);
    die(json_encode(array('success' => FALSE)));
  }
  return $mysqli;
}

function dummy_salt($name)
{
  global $db_passwd;
  return base64_encode(substr(hash_hmac('sha256', $name, $db_passwd, TRUE), 0, 16));
}

function valid_salt($salt)
{
  $decoded = base64_decode($salt, TRUE);
  return $decoded !== FALSE && strlen($decoded) >= 16;
}

function find_user($name, $mysqli)
{
  $stmt = $mysqli->prepare("SELECT name, password, auth_verifier, protocol_version, kdf_salt FROM users WHERE name=?");
  $stmt->bind_param('s', $name);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc() ?: NULL;
}

function verify_authentication($user, $pass, $legacy_pass)
{
  // minimal timing mitigation for non existent users
  if (!$user) {
    return password_verify($pass, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
  }
  if ((int) $user['protocol_version'] === 1) {
    return password_verify($legacy_pass, $user['password']);
  }
  return password_verify($pass, $user['auth_verifier']);

}

function authenticated_user($name, $pass, $legacy_pass, $mysqli)
{
  $user = find_user($name, $mysqli);
  return verify_authentication($user, $pass, $legacy_pass) ? $user : NULL;
}
