<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "dutyfixit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $table = $role === "worker" ? "workers" : "clients";

    // Secure query
    $stmt = $conn->prepare("SELECT * FROM $table WHERE phone = ? OR name = ?");
    if (!$stmt) {
        die("SQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // If passwords are hashed, use this:
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $role;

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='login.html';</script>";
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
