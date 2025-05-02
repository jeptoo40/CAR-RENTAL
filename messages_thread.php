<?php
// Database connection
$host = "localhost";
$dbname = "user_system";
$username = "root";  // Your MySQL username
$password = "1234";  // Your MySQL password

$conn = new mysqli($host, $username, $password, $dbname);  // ✅ Connection line added

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages for a specific email
$email = isset($_GET['email']) ? $_GET['email'] : 'user@example.com';
$sql = "SELECT * FROM message_threads WHERE email = ? ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?php echo htmlspecialchars($email); ?></title>
    <style>
        <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #333;
    }

    #chat-box {
        width: 100%;
        max-width: 600px;
        margin: 0 auto 20px auto;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 15px;
        height: 400px;
        overflow-y: auto;
    }

    .user, .admin {
        margin-bottom: 15px;
        max-width: 75%;
        padding: 10px;
        border-radius: 10px;
        position: relative;
        clear: both;
    }

    .user {
        background-color: #dcf8c6;
        margin-left: auto;
        text-align: right;
    }

    .admin {
        background-color: #f1f0f0;
        margin-right: auto;
        text-align: left;
    }

    .user p, .admin p {
        margin: 5px 0;
        font-size: 14px;
    }

    .user small, .admin small {
        display: block;
        font-size: 10px;
        color: #888;
        margin-top: 5px;
    }

    form#chat-form {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    form#chat-form select, form#chat-form textarea, form#chat-form button {
        font-size: 14px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    form#chat-form textarea {
        resize: none;
        height: 80px;
    }

    form#chat-form button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    form#chat-form button:hover {
        background-color: #45a049;
    }
</style>

    </style>
</head>
<body>
<h2>Chat with Admin</h2>

    <div id="chat-box">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="<?php echo $row['sender']; ?>">
                <strong><?php echo ucfirst($row['sender']); ?>:</strong>
                <p><?php echo htmlspecialchars($row['message']); ?></p>
                <small><?php echo $row['sent_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form id="chat-form">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="sender" value="<?php echo htmlspecialchars($_GET['role'] ?? 'user'); ?>">

        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>

    <div style="text-align: center; margin-bottom: 20px;">
    <form action="index.html" method="post">
        <button type="submit" style="padding: 8px 16px; background-color: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Logout
        </button>
    </form>
</div>

    <script>
        // JavaScript for handling form submission and AJAX polling
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
                document.querySelector('textarea').value = '';
            });
        });

        function fetchMessages() {
            fetch('fetch_messages.php?email=<?php echo urlencode($email); ?>')
            .then(response => response.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
            });
        }

        setInterval(fetchMessages, 2000); // Poll every 2 seconds
    </script>
</body>
</html>
<?php
$conn->close();
?>
