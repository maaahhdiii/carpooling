git remote add origin https://github.com/<your-username>/carpooling.git<?php
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["request_id"])) {
    $request_id = $conn->real_escape_string($data["request_id"]);

    // Update the request status to "rejected"
    $sql = "UPDATE ride_requests SET status = 'rejected' WHERE id = '$request_id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Ride request rejected successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>