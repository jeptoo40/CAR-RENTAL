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

// Check if the form is submitted for entering email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $user_email = $_POST['email'];

    // Fetch the message for this email
    $sql = "SELECT * FROM contact_messages WHERE email = :email ORDER BY sent_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $user_email);
    $stmt->execute();
    
    // Fetch message and check if a result exists
    $message = $stmt->fetch();
}

// If the user submits a reply (this handles the reply submission from the user)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply']) && $message) {
    $user_reply = $_POST['reply'];

    // Check if the reply is not empty
    if (empty($user_reply)) {
        echo "<p style='color: red;'>Please enter a reply.</p>";
    } else {
        // Now let's insert the user reply into the message_threads table for the chat
        try {
            // Insert into message_threads table
            $insertChat = "INSERT INTO message_threads (email, sender, message) 
                           VALUES (:email, 'user', :message)";
            $chatStmt = $pdo->prepare($insertChat);
            $chatStmt->bindParam(':email', $user_email);
            $chatStmt->bindParam(':message', $user_reply);
            if ($chatStmt->execute()) {
                echo "<p>Reply sent successfully! You can continue your conversation here.</p>";

                // Redirect to chat page (messages_thread.php)
                header("Location: messages_thread.php?email=" . urlencode($user_email));
                exit();
            } else {
                echo "<p style='color: red;'>Sorry, there was an error while replying. Please try again later.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Message Response</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2em;
            color: #4CAF50;
            text-align: center;
        }

        h2 {
            font-size: 1.5em;
            color: #333;
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="email"], textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .message-box p {
            margin: 8px 0;
        }

        .status {
            font-weight: bold;
            color: #ff9800;
        }

        .response {
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #c8e6c9;
            margin-top: 20px;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>View Your Message Response</h1>

    <!-- Form to enter email address -->
    <form method="POST" action="Responded.php">
        <label for="email">Enter Your Email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>

    <?php
    if (isset($message) && $message) {
        $user_name = $message['name'];
        $user_message = $message['message'];
        $response = $message['reply'];
        
        $status = $message['status'];
        $sent_at = $message['sent_at'];
    ?>
        <!-- Display the message and response if found -->
        <div class="message-box">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($user_message)); ?></p>
            <p><strong>Sent At:</strong> <?php echo htmlspecialchars($sent_at); ?></p>
        </div>

        <?php if ($status == 'responded'): ?>
            <div class="response">
                <h3>Admin's Response</h3>
                <p><strong>Reply:</strong><br><?php echo nl2br(htmlspecialchars($response)); ?></p>
            </div>
        <?php else: ?>
            <p class="status"><strong>Status:</strong> Your message is still awaiting a response from the admin.</p>
        <?php endif; ?>

        <!-- Reply form to continue conversation -->
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
            <label for="reply">Your Reply:</label>
            <textarea id="reply" name="reply" required></textarea>
            <button type="submit">Send Reply</button>
        </form>

    <?php
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo "<p class='error-message'>No messages found for this email address. Please try again.</p>";
    }
    ?>
</div>

</body>
</html>
