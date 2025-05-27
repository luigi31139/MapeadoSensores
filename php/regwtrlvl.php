<?php
date_default_timezone_set('America/Mexico_City');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "u404093559_sensors";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$snsrid = $_POST['snsrid'];
$snsrname = $_POST['snsrname'];
$promlvl = $_POST['promlvl'];
$meassure = $_POST['meassure'];
$readdate = date('Y-m-d');
$readtime = date('H:i:s');


$sql = "INSERT INTO sensorreads (snsrname, promlvl, meassure, readdate,readtime,rgsensor_id) VALUES ('$snsrname', '$promlvl', '$meassure', '$readdate','$readtime','$snsrid')";

if ($meassure > $promlvl) {
  $usernumbers = [];
  $result = $conn->query("SELECT usernumber FROM rgusersweb WHERE snsrid = '$snsrid'");
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