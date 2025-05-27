<?php
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: text/plain');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "u404093559_sensors";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    rgsensor.*, 
    s.meassure,
    s.readdate,
    s.readtime
FROM 
    rgsensor
LEFT JOIN 
    (SELECT rgsensor_id, MAX(CONCAT(readdate, ' ', readtime)) AS max_read
     FROM sensorreads
     GROUP BY rgsensor_id
    ) AS latest
ON rgsensor.id = latest.rgsensor_id
LEFT JOIN 
    sensorreads AS s
ON s.rgsensor_id = latest.rgsensor_id 
   AND CONCAT(s.readdate, ' ', s.readtime) = latest.max_read
";

$result = $conn->query($sql);

$coordinates = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {  
        $coordinates[] = 
    ($row['id'] ?? '') . "," .
    ($row['nombre'] ?? '') . "," .
    ($row['lat'] ?? '') . "," .
    ($row['lng'] ?? '') . "," .
    ($row['wtrlvl'] ?? '') . "," .
    ($row['meassure'] ?? '') . "," .
    ($row['readdate'] ?? '') . "," .
    ($row['readtime'] ?? '');;
    }
}


$conn->close();

echo implode("\n", $coordinates);
?>