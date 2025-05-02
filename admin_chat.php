<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied.");
}

$host = "localhost";
$dbname = "user_system";
$username = "root";
$password = "1234";
$conn = new mysqli($host, $username, $password, $dbname);

$email = isset($_GET['email']) ? $_GET['email'] : '';
if (!$email) {
    die("No user selected.");
}

$sql = "SELECT * FROM message_threads WHERE email = ? ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chat with <?php echo htmlspecialchars($email); ?></title>
    <style>
        /* Use the same CSS styling from previous chat CSS */
        <?php include 'chat_style.css'; // move the style there for reuse ?>
    </style>
</head>
<body>
    <h2>Admin Chat with <?php echo htmlspecialchars($email); ?></h2>
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
        <input type="hidden" name="sender" value="admin">
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>

    <script>
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

        setInterval(fetchMessages, 2000);
    </script>
</body>
</html>
