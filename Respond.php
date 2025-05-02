<?php
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

// Check if an ID was provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Fetch the message details from the database
    $sql = "SELECT * FROM contact_messages WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $message = $stmt->fetch();

    if (!$message) {
        die('Message not found.');
    }
} else {
    die('Invalid request.');
}

// Process the reply from the admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply = trim($_POST['reply']);

    if (!empty($reply)) {
        // Update contact_messages
        $sql = "UPDATE contact_messages SET reply = :reply, status = 'responded' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':reply', $reply);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Insert into message_threads
            $chatInsert = $pdo->prepare("INSERT INTO message_threads (email, sender, message, sent_at) VALUES (:email, 'admin', :reply, NOW())");
            $chatInsert->bindParam(':email', $message['email']);
            $chatInsert->bindParam(':reply', $reply);
            $chatInsert->execute();

            // Redirect to the admin-user chat
            header("Location: admin to user chat.php?email=" . urlencode($message['email']));
            exit();
        } else {
            echo "Sorry, there was an error sending your reply.";
        }
    } else {
        echo "Reply cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Message</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1em;
            line-height: 1.6;
        }

        label {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: none;
            margin-bottom: 20px;
            background-color: #f7f7f7;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .message-box {
            background-color: #f2f2f2;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 5px solid #4CAF50;
        }

        .message-box p {
            margin: 0;
        }

        .message-box strong {
            font-weight: bold;
        }

        .message-box .time {
            font-size: 0.9em;
            color: #888;
        }

        .error-message {
            text-align: center;
            color: red;
            font-size: 1.2em;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Respond to Message</h1>

    <!-- Display the user's message -->
    <div class="message-box">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
        <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
        <p class="time"><strong>Sent At:</strong> <?php echo htmlspecialchars($message['sent_at']); ?></p>
    </div>

    <!-- Admin Reply Form -->
    <form method="POST">
        <label for="reply">Your Reply:</label>
        <textarea id="reply" name="reply" required></textarea>
        <div style="display: flex; justify-content: space-between; gap: 10px;">
    <button type="submit" style="flex: 1;">Send Reply</button>
    <a href="Admin Dashboard.php" style="background-color: #f44336; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-size: 1.2em; text-align: center; flex: 1;">Logout</a>
</div>



    </form>
</div>

</body>
</html>
