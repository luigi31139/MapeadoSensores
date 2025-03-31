<?php
date_default_timezone_set('America/Mexico_City');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensors";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$snsrid = $_POST['snsrid'];
$snsrname = $_POST['snsrname'];
$promlvl = $_POST['promlvl'];
$meassure = $_POST['meassure'];
$readdate = date('Y-m-d H:i:s');


$sql = "INSERT INTO sensorreads (snsrid, snsrname, promlvl, meassure, readdate) VALUES ('$snsrid', '$snsrname', '$promlvl', '$meassure', '$readdate')";

if ($meassure > $promlvl) {
  $usernumbers = [];
  $result = $conn->query("SELECT usernumber FROM rgusers WHERE snsrid = '$snsrid'");
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $usernumbers[] = $row['usernumber'];
      }
  }

  include 'alertusers.php';
}

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>