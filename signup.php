<?php
// Start session (optional if you want to auto-login after signup)
// session_start();

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "dutyfixit";

$conn = new mysqli($servername, $username, $password, $database);

// Check DB connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle POST form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $role = $_POST["role"];

  if ($role === "client") {
    $name     = $_POST["name"];
    $phone    = $_POST["phone"];
    $location = $_POST["location"];
    $address  = $_POST["address"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clients (name, phone, location, address, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone, $location, $address, $password);

    if ($stmt->execute()) {
      echo "<script>alert('Client registration successful!'); window.location.href='login.html';</script>";
      exit();
    } else {
      echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();

  } elseif ($role === "worker") {
    $name       = $_POST["name"];
    $phone      = $_POST["phone"];
    $domain     = $_POST["domain"];
    $experience = (int) $_POST["experience"]; // cast to int
    $location   = $_POST["location"];
    $address    = $_POST["address"];
    $password   = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // File upload check
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
      $imageName = basename($_FILES['image']['name']);
      $imageTmp  = $_FILES['image']['tmp_name'];
      $uploadDir = "uploads/";

      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }

      $imagePath = $uploadDir . $imageName;

      if (move_uploaded_file($imageTmp, $imagePath)) {
        // Insert worker
        $stmt = $conn->prepare("INSERT INTO workers (name, phone, domain, experience, location, address, image, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssissss", $name, $phone, $domain, $experience, $location, $address, $imagePath, $password);

        if ($stmt->execute()) {
          echo "<script>alert('Worker registration successful!'); window.location.href='login.html';</script>";
          exit();
        } else {
          echo "<script>alert('Database Error: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close();
      } else {
        echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
      }
    } else {
      echo "<script>alert('Please upload a valid profile photo.'); window.history.back();</script>";
    }
  }
}

$conn->close();
?>
