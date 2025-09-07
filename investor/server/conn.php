<?php

  $hostname = "localhost";
  $username = "nairiyke_austin";
  $password = "@iamaustin1969";
  $dbname = "nairiyke_quiz";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }

?>