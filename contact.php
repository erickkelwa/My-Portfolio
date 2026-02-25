<?php
error_reporting(0); // Prevent PHP warnings from breaking JSON response
// contact.php - Backend for Main Portfolio Contact Form
// This script connects to the XAMPP MySQL database

// 1. DATABASE CONFIGURATION
$servername = "localhost";
$username = "root";     // Default XAMPP username
$password = "";         // Default XAMPP password
$dbname = "portfolio_db";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (e.g. VS Code Live Server)
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // Handle preflight
}

try {
    // 2. CREATE CONNECTION
    $conn = @new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error . ". Please make sure MySQL is started in XAMPP!");
    }

    // 3. CREATE DATABASE
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->query($sql);
    $conn->select_db($dbname);

    // 4. CREATE TABLE
    $sql = "CREATE TABLE IF NOT EXISTS portfolio_messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

    // 5. HANDLE DATA
    $inputData = json_decode(file_get_contents('php://input'), true);

    if ($inputData) {
        $name = $conn->real_escape_string($inputData['name']);
        $email = $conn->real_escape_string($inputData['email']);
        $message = $conn->real_escape_string($inputData['message']);

        $sql = "INSERT INTO portfolio_messages (name, email, message) 
                VALUES ('$name', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode([
                "status" => "success",
                "message" => "Thank you, Erick will get back to you soon!"
            ]);
        } else {
            throw new Exception("Error during save: " . $conn->error);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Empty data"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
