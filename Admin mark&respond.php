<?php
// Database connection settings
$host = 'localhost'; // Your database host
$dbname = 'user_system'; // Correct database name
$username = 'root'; // Your database username
$password = '1234'; // Your database password

// Create a new PDO instance to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch messages from the database
$sql = "SELECT * FROM contact_messages ORDER BY sent_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll();

// Check if an action (mark as read or responded) is requested
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    // Ensure action is either 'read' or 'responded'
    if ($action == 'read' || $action == 'responded') {
        // Update the status of the message
        $updateSql = "UPDATE contact_messages SET status = :status WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':status', $action);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        // Redirect back to the dashboard to prevent re-posting the same action
        header('Location: admin_dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Contact Messages</title>
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

        .status {
            font-weight: bold;
        }

    </style>
</head>
<body>

<header>
    <h1>Admin Dashboard - Contact Messages</h1>
</header>

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
                <td class="status">
                    <?php echo ucfirst($message['status']); ?>
                </td>
                <td class="message-actions">
                    <!-- Mark as Read -->
                    <?php if ($message['status'] == 'unread'): ?>
                        <a href="admin_dashboard.php?action=read&id=<?php echo $message['id']; ?>">Mark as Read</a>
                    <?php endif; ?>

                    <!-- Mark as Responded -->
                    <?php if ($message['status'] != 'responded'): ?>
                        <a href="admin_dashboard.php?action=responded&id=<?php echo $message['id']; ?>">Mark as Responded</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
