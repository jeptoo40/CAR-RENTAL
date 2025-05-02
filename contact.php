<?php
// Database connection settings
$host = 'localhost';
$dbname = 'user_system';
$username = 'root';
$password = '1234';

// Create a new PDO instance to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Check if the same name, email, and message already exist in the database
    $sql = "SELECT COUNT(*) FROM contact_messages WHERE name = :name AND email = :email AND message = :message";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // If no duplicate is found, insert the new message
    if ($count == 0) {
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            $message_id = $pdo->lastInsertId();

            // Display message with link
            echo "<p style='color: green; font-weight: bold;'>Thank you for your message!</p>";
            echo "<p><a href='user responce page view.php?id=$message_id' style='color: blue;'>Click here to check the status of your message</a></p>";

        } else {
            echo "<p style='color: red;'>Sorry, there was an error submitting your message. Please try again later.</p>";
        }
    } else {
        echo "<p style='color: orange;'>Your message has already been submitted. Please check your inbox for a response.</p>";
    }
}
?>
