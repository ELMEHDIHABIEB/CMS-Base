<?php
require_once 'config.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'] ?? null;

if (!$logged_in_user_id) {
    die("You need to be logged in to view messages.");
}

// Fetch messages where the logged-in user is either the sender or the receiver
$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.sent_at, 
               COALESCE(CONCAT_WS(' ', u1.first_name, u1.last_name), u1.username) AS sender_name,
               COALESCE(CONCAT_WS(' ', u2.first_name, u2.last_name), u2.username) AS receiver_name
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.sent_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $logged_in_user_id, $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<?php include 'header.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header text-center"><i class="bi bi-chat-left-text-fill"></i> Messages</div>
        <div class="card-body">
            <?php if (empty($messages)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($messages as $message): ?>
                        <li class="list-group-item">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($message['sender_name']); ?><br>
                            <strong><p><?php echo htmlspecialchars($message['message']); ?></p></strong>
                            <small><i class="bi bi-clock"></i>  <?php echo htmlspecialchars($message['sent_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
