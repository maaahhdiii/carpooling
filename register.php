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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["name"]) && isset($data["email"]) && isset($data["phone"]) && isset($data["password"])) {
    $name = $conn->real_escape_string($data["name"]);
    $email = $conn->real_escape_string($data["email"]);
    $phone = $conn->real_escape_string($data["phone"]);
    $password = md5($data["password"]); // Use MD5 for simplicity (not recommended for production)
    $is_driver = isset($data["is_driver"]) && $data["is_driver"] ? 1 : 0;

    // Check if the email already exists
    $checkEmailSql = "SELECT id FROM users WHERE email = '$email'";
    $checkEmailResult = $conn->query($checkEmailSql);

    if ($checkEmailResult->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists."]);
    } else {
        // Insert the user into the database
        $sql = "INSERT INTO users (name, email, phone, password, is_driver) VALUES ('$name', '$email', '$phone', '$password', $is_driver)";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registration successful."]);
        } else {
            // Log the error
            error_log("Database error: " . $conn->error);
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>