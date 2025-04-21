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

// Get the driver ID from the query parameters
$driver_id = isset($_GET['driver_id']) ? $conn->real_escape_string($_GET['driver_id']) : '';

if (!empty($driver_id)) {
    $sql = "SELECT rr.id AS request_id, rr.status, u.name AS passenger_name, u.phone AS passenger_phone, 
                   ro.pickup_location, ro.destination, ro.departure_time 
            FROM ride_requests rr
            JOIN driver_offers ro ON rr.ride_id = ro.id
            JOIN users u ON rr.passenger_id = u.id
            WHERE ro.driver_id = '$driver_id' AND rr.status = 'pending'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        echo json_encode(["success" => true, "requests" => $requests]);
    } else {
        echo json_encode(["success" => false, "message" => "No ride requests found."]);
    }
} else {
    echo json_encode(["error" => "Driver ID is required."]);
}

$conn->close();
?>