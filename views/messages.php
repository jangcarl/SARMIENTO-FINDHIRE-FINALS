<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$messages = fetchMessages($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles/messages.css">
</head>
<body>
    <div class="container">
        <h1>Messages</h1>

        <h2>Inbox</h2>
        <table>
        <tr>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Message</th>
            <th>Sent At</th>
        </tr>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?= htmlspecialchars($message['sender_name']) ?></td> 
                <td><?= htmlspecialchars($message['receiver_name']) ?></td>
                <td><?= htmlspecialchars($message['content']) ?></td>
                <td><?= htmlspecialchars($message['sent_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </table>

        <!-- Send a Message Form -->
        <h2>Send a Message</h2>
        <form action="core/handleforms.php" method="POST">
            <label for="receiver_username">Recipient Name:</label>
            <input type="text" name="receiver_username" id="receiver_username" required>
            <br>
            <textarea name="content" placeholder="Write your message here..." required></textarea>
            <br>
            <button type="submit" name="send_message">Send</button>
        </form>

        <br><br>
    </div>
</body>
</html>