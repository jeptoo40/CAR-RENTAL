<?php
session_start();

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

// Get user email from URL
if (isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_GET['email'];

    // Fetch chat messages
    $stmt = $pdo->prepare("SELECT * FROM message_threads WHERE email = :email ORDER BY sent_at ASC");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $messages = $stmt->fetchAll();
} else {
    die("Invalid or missing email parameter.");
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Send reply
    if (isset($_POST['reply'])) {
        $reply = trim($_POST['reply']);

        if (!empty($reply)) {
            $stmt = $pdo->prepare("INSERT INTO message_threads (email, sender, message, sent_at) VALUES (:email, 'admin', :message, NOW())");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $reply);
            $stmt->execute();

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Reply cannot be empty.']);
        }
        exit;
    }

    // Logout
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'logged_out']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin-User Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .chat-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .chat-box {
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }

        .message {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 8px;
            max-width: 70%;
            clear: both;
        }

        .admin {
            background-color: #e1f5fe;
            float: right;
            text-align: right;
        }

        .user {
            background-color: #dcedc8;
            float: left;
            text-align: left;
        }

        .timestamp {
            font-size: 0.8em;
            color: #777;
            margin-top: 5px;
        }

        h2 {
            text-align: center;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .reply-section {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        textarea {
            flex: 1;
            min-height: 60px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .send-btn {
            background-color: #4CAF50;
            color: white;
        }

        .send-btn:hover {
            background-color: #45a049;
        }

        .logout-btn {
            background-color: #f44336;
            color: white;
        }

        .logout-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <h2>Chat with <?php echo htmlspecialchars($email); ?></h2>

    <div class="chat-box" id="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['sender']; ?> clearfix">
                <div><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                <div class="timestamp"><?php echo $msg['sent_at']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="reply-section">
        <textarea id="reply" placeholder="Type your reply..."></textarea>
        <button class="btn send-btn" id="send-reply">Send Reply</button>
        <button class="btn logout-btn" id="logout">Logout</button>
    </div>
</div>

<script>
    document.getElementById('send-reply').addEventListener('click', function () {
        const reply = document.getElementById('reply').value;

        if (reply.trim() === '') {
            alert('Please type a reply.');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (response.status === 'success') {
                    const newMessage = document.createElement('div');
                    newMessage.classList.add('message', 'admin', 'clearfix');
                    newMessage.innerHTML = `<div>${reply}</div><div class="timestamp">Just now</div>`;
                    document.getElementById('chat-box').appendChild(newMessage);

                    document.getElementById('reply').value = '';
                    document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
                } else {
                    alert(response.message || 'Something went wrong.');
                }
            }
        };
        xhr.send('reply=' + encodeURIComponent(reply));
    });

    document.getElementById('logout').addEventListener('click', function () {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (response.status === 'logged_out') {
                    window.location.href = 'Admin Dashboard.php'; 
                } else {
                    alert('Something went wrong while logging out.');
                }
            }
        };
        xhr.send('logout=true');
    });
</script>

</body>
</html>
