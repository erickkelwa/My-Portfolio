<?php
error_reporting(0); // Prevent PHP warnings from breaking JSON response
// contact.php - Backend for Sitambili FC Contact Form
// This script connects to the XAMPP MySQL database

// 1. DATABASE CONFIGURATION
$servername = "localhost";
$username = "root";     // Default XAMPP username
$password = "";         // Default XAMPP password (usually empty)
$dbname = "sitambili_db";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

try {
    // 2. CREATE CONNECTION
    $conn = @new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("MySQL Connection Failed: " . $conn->connect_error . ". Is MySQL started in XAMPP?");
    }

    // 3. CREATE DATABASE IF NOT EXISTS
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->query($sql);
    $conn->select_db($dbname);

    // 4. CREATE TABLE IF NOT EXISTS
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200),
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

    // 5. HANDLE DATA (POST REQUEST)
    $inputData = json_decode(file_get_contents('php://input'), true);

    if ($inputData) {
        $name = $conn->real_escape_string($inputData['name']);
        $email = $conn->real_escape_string($inputData['email']);
        $subject = $conn->real_escape_string($inputData['subject']);
        $message = $conn->real_escape_string($inputData['message']);

        $sql = "INSERT INTO messages (name, email, subject, message) 
                VALUES ('$name', '$email', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode([
                "status" => "success",
                "message" => "Message stored in XAMPP database successfully!"
            ]);
        } else {
            throw new Exception("Error: " . $conn->error);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No data received"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
