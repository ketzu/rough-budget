<?php
include 'auth.php';

if($auth == TRUE) {
    $stmt2 = $mysqli->prepare("DELETE FROM users WHERE name=?");
    $stmt2->bind_param('s',$name);
    $stmt2->execute();
    $success = TRUE;
}

$mysqli->close();
echo json_encode(array('success' => $success));
?>