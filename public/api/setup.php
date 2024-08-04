<?php
include 'conf.php';

$mysqli->query("
CREATE TABLE IF NOT EXISTS `users` (
  name varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  lastaccessed timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  content mediumtext,
  PRIMARY KEY (name)
)");