<?php
require_once 'config.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'] ?? null;
$receiver_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if (!$logged_in_user_id || !$receiver_id) {
    die("Invalid request.");
}

// Fetch messages for a specific conversation
$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.sent_at, 
               COALESCE(CONCAT_WS(' ', u1.first_name, u1.last_name), u1.username) AS sender_name,
               COALESCE(CONCAT_WS(' ', u2.first_name, u2.last_name), u2.username) AS receiver_name,
               COALESCE(u1.avatar, '/avatar/default-avatar.png') AS sender_avatar,
               COALESCE(u2.avatar, '/avatar/default-avatar.png') AS receiver_avatar
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $logged_in_user_id, $receiver_id, $receiver_id, $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $msg): ?>
        <div class="message <?php echo $msg['sender_id'] == $logged_in_user_id ? 'outgoing' : 'incoming'; ?>">
            <div class="d-flex align-items-start mb-3">
             <?php if ($msg['sender_id'] == $logged_in_user_id): ?>
    <img src="<?php echo htmlspecialchars($profile_user['user_avatar'] ?: '/avatar/default-avatar.png'); ?>" 
         alt="Your Avatar" 
         class="img-thumbnail rounded-circle me-3" 
         style="width: 40px; height: 40px; object-fit: cover;">
<?php else: ?>
    <img src="<?php echo htmlspecialchars($msg['sender_avatar'] ?: '/avatar/default-avatar.png'); ?>" 
         alt="Sender Avatar" 
         class="img-thumbnail rounded-circle me-3" 
         style="width: 40px; height: 40px; object-fit: cover;">
<?php endif; ?>


                <div>
                    <p><a href="profile.php?user_id=<?php echo htmlspecialchars($msg['sender_id']); ?>" class="text-light"><?php echo htmlspecialchars($msg['sender_name']); ?></a></p><br><p><strong> <?php echo htmlspecialchars($msg['message']); ?></strong></p>
                    <small><?php echo date('Y-m-d H:i', strtotime($msg['sent_at'])); ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No messages found.</p>
<?php endif; ?>

<?php
$conn->close();
?>
