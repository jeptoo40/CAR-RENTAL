<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "user_system"; // change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate random admin ID (optional, if we decide to fall back to generating it automatically)
function generateAdminId($length = 9) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $admin_id = $conn->real_escape_string($_POST['id']);  // Admin ID entered by the user
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // You will hash this password

    // Check if ID, email, or username already exists
    $sql = "SELECT * FROM admins WHERE id='$admin_id' OR email='$email' OR username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "The Admin ID, Email, or Username already exists! Please choose a different ID.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new admin with the provided ID into the database
        $sql = "INSERT INTO admins (id, username, email, password) 
                VALUES ('$admin_id', '$username', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login page after successful signup
            header("Location: admin_login.html"); // replace with the path to your login page
            exit(); // Make sure to call exit to stop further execution after the redirect
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
