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

// Get user ID and role from query parameters
$user_id = isset($_GET['user_id']) ? $conn->real_escape_string($_GET['user_id']) : '';
$is_driver = isset($_GET['is_driver']) ? $conn->real_escape_string($_GET['is_driver']) : '';

if (!empty($user_id)) {
    if ($is_driver === "1") {
        // Fetch rides for the driver
        $sql = "SELECT ro.id AS ride_id, ro.pickup_location, ro.destination, ro.departure_time, ro.price, ro.available_seats
                FROM driver_offers ro
                WHERE ro.driver_id = '$user_id'";
    } else {
        // Fetch rides for the passenger
        $sql = "SELECT ro.id AS ride_id, ro.pickup_location, ro.destination, ro.departure_time, ro.price, ro.available_seats
                FROM ride_requests rr
                JOIN driver_offers ro ON rr.ride_id = ro.id
                WHERE rr.passenger_id = '$user_id' AND rr.status = 'accepted'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rides = [];
        while ($row = $result->fetch_assoc()) {
            $rides[] = $row;
        }
        echo json_encode(["success" => true, "rides" => $rides]);
    } else {
        echo json_encode(["success" => false, "message" => "No rides found."]);
    }
} else {
    echo json_encode(["error" => "User ID is required."]);
}

$conn->close();
?>