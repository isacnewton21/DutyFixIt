<?php
session_start();

// Redirect unauthenticated users
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}
?>

<!-- Load the HTML layout -->
<?php include 'dashboard.html'; ?>
