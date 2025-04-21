<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "carpooling";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Get filters from query parameters
$pickup = isset($_GET['pickup']) ? $conn->real_escape_string($_GET['pickup']) : '';
$destination = isset($_GET['destination']) ? $conn->real_escape_string($_GET['destination']) : '';
$date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

// Build the SQL query with filters
$sql = "SELECT * FROM driver_offers WHERE 1=1";

if (!empty($pickup)) {
    $sql .= " AND pickup_location LIKE '%$pickup%'";
}
if (!empty($destination)) {
    $sql .= " AND destination LIKE '%$destination%'";
}
if (!empty($date)) {
    $sql .= " AND DATE(departure_time) = '$date'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $offers = [];
    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }
    echo json_encode(["success" => true, "offers" => $offers]);
} else {
    echo json_encode(["success" => false, "message" => "No offers found."]);
}

$conn->close();
?>