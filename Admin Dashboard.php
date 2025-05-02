<?php
// Database connection settings
$host = 'localhost'; 
$dbname = 'user_system'; 
$username = 'root'; 
$password = '1234'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Now it's safe to query for new/unread messages in contact_messages
    $newStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
    $newStmt->execute();
    $newCount = $newStmt->fetchColumn();

    // Query to fetch all contact messages
    $messagesStmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY sent_at DESC");
    $messagesStmt->execute();
    $messages = $messagesStmt->fetchAll(); // Fetch results as an array

    // Query to fetch all message threads
    $threadsStmt = $pdo->prepare("SELECT * FROM message_threads ORDER BY sent_at DESC");
    $threadsStmt->execute();
    $messageThreads = $threadsStmt->fetchAll(); // Fetch results as an array

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #0066cc;
            color: white;
            padding: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #0066cc;
            color: white;
        }

        .message-actions a {
            margin-right: 10px;
            color: #0066cc;
            text-decoration: none;
        }

        .message-actions a:hover {
            text-decoration: underline;
        }

        /* Styling for the logout button */
        button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #d32f2f;
        }

    </style>
</head>
<body>

<header>
<h1>
    Admin Dashboard - Contact Messages
    <?php if ($newCount > 0): ?>
        <span style="background: red; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.6em; margin-left: 10px;">
            🔔 <?php echo $newCount; ?> New
        </span>
    <?php endif; ?>
</h1>

<!-- Logout Button -->
<form action="logout.php" method="POST" style="display: inline-block; margin-top: 10px;">
    <button type="submit">Logout</button>
</form>

</header>

<!-- Section to Display Contact Messages -->
<h2>Contact Messages</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Sent At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo $message['id']; ?></td>
                <td><?php echo $message['name']; ?></td>
                <td><?php echo $message['email']; ?></td>
                <td><?php echo $message['message']; ?></td>
                <td><?php echo $message['sent_at']; ?></td>
                <td style="color: <?php echo $message['status'] === 'unread' ? 'red' : 'green'; ?>;">
                    <?php echo ucfirst($message['status']); ?>
                    <?php if ($message['status'] === 'unread'): ?>
                        <strong style="color: red;"> (New)</strong>
                    <?php endif; ?>
                </td>

                <td class="message-actions">
                    <a href="respond.php?id=<?php echo $message['id']; ?>">Respond</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Section to Display Message Threads -->
<h2>Message Threads</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Sender</th>
            <th>Message</th>
            <th>Sent At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messageThreads as $thread): ?>
            <tr>
                <td><?php echo $thread['id']; ?></td>
                <td><?php echo $thread['email']; ?></td>
                <td><?php echo ucfirst($thread['sender']); ?></td>
                <td><?php echo $thread['message']; ?></td>
                <td><?php echo $thread['sent_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
