<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "user_system"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $admin_id = $conn->real_escape_string($_POST['id']);  // Admin ID entered by the user
    $password = $_POST['password']; // Password entered by the user

    // Query to get the user based on the admin_id
    $sql = "SELECT * FROM admins WHERE id='$admin_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct
            echo "Login successful!"; // Redirect to the admin dashboard or home page
             header("Location: Admin Dashboard.php"); // Uncomment to redirect
        } else {
            // Password is incorrect
            echo "Incorrect password. Please try again.";
        }
    } else {
        echo "Admin ID does not exist.";
    }
}

$conn->close();
?>
