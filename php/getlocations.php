<?php
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: text/plain');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensors";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    rgsensor.*, 
    s.meassure
FROM 
    rgsensor
LEFT JOIN 
    (SELECT snsrid, MAX(readdate) AS max_readdate
     FROM sensorreads
     GROUP BY snsrid
    ) AS latest
ON rgsensor.id = latest.snsrid
LEFT JOIN 
    sensorreads AS s
ON s.snsrid = latest.snsrid AND s.readdate = latest.max_readdate
";


$result = $conn->query($sql);

$coordinates = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        $coordinates[] = $row['id'] . "," . $row['nombre'] . "," . $row['lat'] . "," . $row['lng'] . "," . $row['wtrlvl'] . "," . $row['meassure'];
    }
}


$conn->close();

echo implode("\n", $coordinates);
?>