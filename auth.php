<?php
// filepath: C:\xampp\htdocs\carpooling\auth.php
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["email"]) && isset($data["password"])) {
    $email = $conn->real_escape_string($data["email"]);
    $password = md5($data["password"]); // Use MD5 for simplicity (not recommended for production)

    // Query the database
    $sql = "SELECT id, name, email, is_driver FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user["is_driver"] = (bool)$user["is_driver"]; // Convert is_driver to boolean
        echo json_encode([
            "success" => true,
            "user" => $user,
            "token" => base64_encode($user["email"]) // Simple token for demonstration
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>