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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["ride_id"])) {
    $ride_id = $conn->real_escape_string($data["ride_id"]);

    // Delete the ride offer
    $sql = "DELETE FROM driver_offers WHERE id = '$ride_id'";
    if ($conn->query($sql) === TRUE) {
        // Also delete associated ride requests
        $deleteRequestsSql = "DELETE FROM ride_requests WHERE ride_id = '$ride_id'";
        $conn->query($deleteRequestsSql);

        echo json_encode(["success" => true, "message" => "Ride offer canceled successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>