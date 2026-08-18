<?php
header('Content-type: application/json');
ini_set('display_errors', 0);

include 'conf.php';

function extract_from_request($name) {
  static $json = NULL;
  if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== FALSE) {
    if ($json === NULL) $json = json_decode(file_get_contents('php://input'), TRUE) ?: array();
    return $json[$name] ?? '';
  }
  return $_POST[$name] ?? '';
}

function database() {
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

function dummy_verifier() {
  return '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
}

function dummy_salt($name) {
  global $db_passwd;
  return base64_encode(substr(hash_hmac('sha256', $name, $db_passwd, TRUE), 0, 16));
}

function valid_salt($salt) {
  $decoded = base64_decode($salt, TRUE);
  return $decoded !== FALSE && strlen($decoded) >= 16;
}

function find_user($name, $mysqli) {
  $stmt = $mysqli->prepare("SELECT name, password, auth_verifier, protocol_version, kdf_salt FROM users WHERE name=?");
  $stmt->bind_param('s', $name);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc() ?: NULL;
}

function verify_authentication($user, $pass, $legacy_pass) {
  $verifier = $user['auth_verifier'] ?? dummy_verifier();
  $valid = password_verify($pass, $verifier);
  if ($user && (int)$user['protocol_version'] === 1 && $legacy_pass !== '') {
    $valid = password_verify($legacy_pass, $user['password']);
  }
  return $valid;
}

function create_session($name, $mysqli) {
  $token = bin2hex(random_bytes(32));
  $token_hash = hash('sha256', $token);
  $expires = gmdate('Y-m-d H:i:s', time() + 86400);
  $stmt = $mysqli->prepare("INSERT INTO sessions (token_hash, username, expires_at) VALUES (?,?,?)");
  $stmt->bind_param('sss', $token_hash, $name, $expires);
  $stmt->execute();
  return $token;
}

function session_user($token, $mysqli) {
  if ($token === '') return NULL;
  $token_hash = hash('sha256', $token);
  $stmt = $mysqli->prepare("SELECT u.name FROM sessions s JOIN users u ON u.name=s.username WHERE s.token_hash=? AND s.expires_at > UTC_TIMESTAMP()");
  $stmt->bind_param('s', $token_hash);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}
