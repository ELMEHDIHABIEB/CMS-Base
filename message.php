<?php
require_once 'config.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'] ?? null;
$receiver_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if (!$logged_in_user_id) {
    die("You need to be logged in to view messages.");
}

// Fetch receiver user data if a user_id is provided
$receiver_user = null;
if ($receiver_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $receiver_user = $result->fetch_assoc();
    } else {
        die("Invalid user ID.");
    }
}

// Fetch messages where the logged-in user is either the sender or the receiver
$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.sent_at, 
               COALESCE(CONCAT_WS(' ', u1.first_name, u1.last_name), u1.username) AS sender_name,
               COALESCE(CONCAT_WS(' ', u2.first_name, u2.last_name), u2.username) AS receiver_name
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        WHERE (m.sender_id = ? OR m.receiver_id = ?)
        AND ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
        ORDER BY m.sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiiii', $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $receiver_id, $receiver_id, $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $receiver_id) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param('iis', $logged_in_user_id, $receiver_id, $message);
        $insert_stmt->execute();
        $insert_stmt->close();
        header("Location: message.php?user_id=$receiver_id");
        exit;
    } else {
        $error_message = "Message cannot be empty.";
    }
}

$conn->close();
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main>
<div class="card bg-dark text-light">
    <div class="card-body">
        <h4 class="card-title mb-4"><i class="bi bi-chat-left-text-fill"></i> Messages</h4>
        <?php if ($receiver_user): ?>
            <h5>Conversation with <?php echo htmlspecialchars($receiver_user['first_name']); ?></h5>
            <div class="message-timeline">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?php echo $msg['sender_id'] == $logged_in_user_id ? 'outgoing' : 'incoming'; ?>">
                            <p><strong><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
                            <small><?php echo date('Y-m-d H:i', strtotime($msg['sent_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No messages found.</p>
                <?php endif; ?>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <textarea name="message" class="form-control" rows="3" placeholder="Type your message here..."></textarea>
                </div>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        <?php else: ?>
            <p>Please select a user to start a conversation.</p>
        <?php endif; ?>
    </div>
</div>
</main>
<?php include 'footer.php'; ?>

<style>
.message-timeline {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 1em;
}
.message {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
.incoming {
    background-color: #2c6a4e;
}
.outgoing {
    background-color: #2d465a;
    text-align: left;
}
</style>
