<?php
include 'auth.php';

$success = $auth;

$mysqli->close();
echo json_encode(array('success' => $success));
?>