<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "u404093559_sensors";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['csv'])) {
    $csv = $_POST['csv'];
    $lines = explode("\n", $csv);
    $header = str_getcsv(array_shift($lines));
    $data = str_getcsv(array_shift($lines));

    $snsrid = $data[0];
    $username = $data[1];
    $userlat = $data[2];
    $userlng = $data[3];
    $usernumber = $data[4];

    $sql = "INSERT INTO rgusers (snsrid, username, userlat, userlng, usernumber) VALUES ('$snsrid','$username','$userlat', '$userlng', '$usernumber')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>