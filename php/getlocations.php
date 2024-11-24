<?php
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: text/plain');

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

$sql = "SELECT lat, lng, wtrlvl, nombre FROM rgsensor";
$result = $conn->query($sql);

$coordinates = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $coordinates[] = $row['lat'] . "," . $row['lng'] . "," . $row['wtrlvl'] . "," . $row['nombre'];
    }
}

$conn->close();

echo implode("\n", $coordinates);
?>