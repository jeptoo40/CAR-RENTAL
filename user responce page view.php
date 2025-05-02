<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost';
$dbname = 'user_system';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = null;
$user_email = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $user_email = $_POST['email'];

    // Debugging: Output the submitted email
    echo "<p>Debug: Email Submitted: " . htmlspecialchars($user_email) . "</p>";

    // Fetch the message for this email
    $sql = "SELECT * FROM contact_messages WHERE email = :email ORDER BY sent_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $user_email);
    $stmt->execute();
    
    // Fetch message and check if a result exists
    $message = $stmt->fetch();

    // Debugging: Output the fetched message data
    echo "<pre>";
    var_dump($message);
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Message Response</title>
</head>
<body>

<h1>View Your Message Response</h1>

<!-- Form to enter email address -->
<form method="POST" action="Responded.php">
    <label for="email">Enter Your Email:</label>
    <input type="email" name="email" id="email" required>
    <button type="submit">Submit</button>
</form>

<?php
// If message is found, display it
if ($message) {
    $user_name = $message['name'];
    $user_message = $message['message'];
    $response = $message['reply'];
    $status = $message['status'];
    $sent_at = $message['sent_at'];
?>
    <h2>Your Message:</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
    <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($user_message)); ?></p>
    <p><strong>Sent At:</strong> <?php echo htmlspecialchars($sent_at); ?></p>

    <?php if ($status == 'responded'): ?>
        <h3>Admin's Response</h3>
        <p><strong>Reply:</strong><br><?php echo nl2br(htmlspecialchars($response)); ?></p>
    <?php else: ?>
        <p><strong>Status:</strong> Your message is still awaiting a response from the admin.</p>
    <?php endif; ?>
<?php
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p style='color: red;'>No messages found for this email address. Please try again.</p>";
}
?>

</body>
</html>
