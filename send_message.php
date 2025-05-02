<?php
// Database connection
$host = "localhost";
$dbname = "user_system";
$username = "root";  // Your MySQL username
$password = "1234";  // Your MySQL password

// ✅ You must connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sender = $_POST['sender'];
    $message = $_POST['message'];

    $sql = "INSERT INTO message_threads (email, sender, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $sender, $message);
    $stmt->execute();
    $stmt->close();
}

// Fetch updated messages
$sql = "SELECT * FROM message_threads WHERE email = ? ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='{$row['sender']}'>
            <strong>" . ucfirst($row['sender']) . ":</strong>
            <p>" . htmlspecialchars($row['message']) . "</p>
            <small>" . $row['sent_at'] . "</small>
          </div>";
}

$stmt->close();
$conn->close();
?>
