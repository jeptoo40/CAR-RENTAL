<?php
// Database connection
$host = "localhost";
$dbname = "user_system";
$username = "root";  // Your MySQL username
$password = "1234";  // Your MySQL password

// ✅ Add this line to connect to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages
$email = isset($_GET['email']) ? $_GET['email'] : 'user@example.com';
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
