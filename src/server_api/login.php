<?php
include 'conf.php';

$success = $auth;

$mysqli->close();
echo json_encode(array('success' => $success));
?>