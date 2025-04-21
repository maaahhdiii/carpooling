<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
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

// Parse the incoming JSON request
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["ride_id"]) && isset($data["passenger_id"])) {
    $ride_id = $conn->real_escape_string($data["ride_id"]);
    $passenger_id = $conn->real_escape_string($data["passenger_id"]);

    // Check if the passenger has already requested this ride
    $checkSql = "SELECT * FROM ride_requests WHERE ride_id = '$ride_id' AND passenger_id = '$passenger_id'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "You have already requested this ride."]);
    } else {
        // Insert the ride request
        $sql = "INSERT INTO ride_requests (ride_id, passenger_id, status) VALUES ('$ride_id', '$passenger_id', 'pending')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Ride request sent successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>