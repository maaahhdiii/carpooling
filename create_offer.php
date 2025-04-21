<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

// Parse the incoming JSON request
$data = json_decode(file_get_contents("php://input"), true);

// Validate the request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["driver_id"]) && isset($data["pickup_location"]) && isset($data["destination"]) && isset($data["departure_time"]) && isset($data["available_seats"]) && isset($data["price"])) {
    $driver_id = $conn->real_escape_string($data["driver_id"]);
    $pickup_location = $conn->real_escape_string($data["pickup_location"]);
    $destination = $conn->real_escape_string($data["destination"]);
    $departure_time = $conn->real_escape_string($data["departure_time"]);
    $available_seats = (int)$data["available_seats"];
    $price = (float)$data["price"];
    $notes = isset($data["notes"]) ? $conn->real_escape_string($data["notes"]) : null;

    // Insert the ride offer into the database
    $sql = "INSERT INTO driver_offers (driver_id, pickup_location, destination, departure_time, available_seats, price, notes) 
            VALUES ('$driver_id', '$pickup_location', '$destination', '$departure_time', $available_seats, $price, '$notes')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Ride offer created successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>