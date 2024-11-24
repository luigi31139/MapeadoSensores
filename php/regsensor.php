<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensors";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['csv'])) {
    $csv = $_POST['csv'];
    $lines = explode("\n", $csv);
    $header = str_getcsv(array_shift($lines));
    $data = str_getcsv(array_shift($lines));

    $nombre = $data[0];
    $lat = $data[1];
    $lng = $data[2];
    $wtrlvl = $data[3];

    $sql = "INSERT INTO rgsensor (nombre, lat, lng, wtrlvl) VALUES ('$nombre', '$lat', '$lng', '$wtrlvl')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>