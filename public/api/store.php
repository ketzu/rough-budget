<?php
include 'auth.php';

if($auth == TRUE) {
    $stmt2 = $mysqli->prepare("UPDATE users SET content=? WHERE name=?");
    $stmt2->bind_param('ss',$data,$name);
    $stmt2->execute();
    $success = TRUE;
}

$mysqli->close();
echo json_encode(array('success' => $success));

?>