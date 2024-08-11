<?php
require_once 'config.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'] ?? null;
$receiver_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if (!$logged_in_user_id) {
    die("You need to be logged in to view messages.");
}

// Fetch the logged-in user's profile information
$profile_sql = "SELECT avatar AS user_avatar FROM users WHERE id = ?";
$profile_stmt = $conn->prepare($profile_sql);
$profile_stmt->bind_param('i', $logged_in_user_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();
$profile_user = $profile_result->fetch_assoc();
$profile_stmt->close();

if ($receiver_id) {
    // Fetch messages for a specific conversation
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.sent_at, 
                   COALESCE(CONCAT_WS(' ', u1.first_name, u1.last_name), u1.username) AS sender_name,
                   COALESCE(CONCAT_WS(' ', u2.first_name, u2.last_name), u2.username) AS receiver_name,
                   u1.avatar AS sender_avatar, u2.avatar AS receiver_avatar
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

    // Handle message sending
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $receiver_id) {
        $message = trim($_POST['message']);
        if (!empty($message)) {
            $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message, sent_at, message_status) VALUES (?, ?, ?, NOW(), 'sent')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('iis', $logged_in_user_id, $receiver_id, $message);
            $insert_stmt->execute();
            $insert_stmt->close();
            echo 'Message sent';
            exit;
        } else {
            $error_message = "Message cannot be empty.";
        }
    }
} else {
    // Fetch all conversations where the logged-in user is the receiver
    $sql = "SELECT m.sender_id, u.username AS sender_username, u.avatar AS sender_avatar,
                   MAX(m.sent_at) AS last_message_time,
                   (SELECT message FROM messages WHERE sender_id = m.sender_id AND receiver_id = ? ORDER BY sent_at DESC LIMIT 1) AS last_message
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.receiver_id = ?
            GROUP BY m.sender_id
            ORDER BY last_message_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $logged_in_user_id, $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $conversations = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main>
    <div class="card bg-dark text-light">
        <div class="card-body">
            <?php if ($receiver_id): ?>
                <h4 class="card-title mb-4"><i class="bi bi-chat-left-text-fill"></i> Conversation with <?php echo htmlspecialchars($receiver_user['first_name']); ?></h4>
                <div id="message-container" class="message-timeline">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="message <?php echo $msg['sender_id'] == $logged_in_user_id ? 'outgoing' : 'incoming'; ?>">
                                <div class="d-flex align-items-start mb-3">
                                    <?php if ($msg['sender_id'] == $logged_in_user_id): ?>
                                        <img src="<?php echo htmlspecialchars($profile_user['user_avatar'] ?: 'default-avatar.png'); ?>" 
                                             alt="Your Avatar" 
                                             class="img-thumbnail rounded-circle me-3" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($msg['sender_avatar'] ?: 'default-avatar.png'); ?>" 
                                             alt="Sender Avatar" 
                                             class="img-thumbnail rounded-circle me-3" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <p><strong><a href="profile.php?user_id=<?php echo htmlspecialchars($msg['sender_id']); ?>" class="text-light"><?php echo htmlspecialchars($msg['sender_name']); ?></a>:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
                                        <small><?php echo date('Y-m-d H:i', strtotime($msg['sent_at'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No messages found.</p>
                    <?php endif; ?>
                </div>
                <form id="message-form">
                    <div class="form-group">
                        <textarea id="message-input" name="message" class="form-control" rows="3" placeholder="Type your message here..."></textarea>
                    </div>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
                <div id="typing-status" class="text-muted"></div>
            <?php else: ?>
                <h4 class="card-title mb-4"><i class="bi bi-inbox"></i> Inbox</h4>
                <div class="card-header text-center">
                    <i class="bi bi-chat-left-text-fill"></i> Conversations
                </div>
                <div class="card-body">
                    <?php if (empty($conversations)): ?>
                        <p>No conversations found.</p>
                    <?php else: ?>
                        <?php foreach ($conversations as $conversation): ?>
                            <a href="message.php?user_id=<?php echo $conversation['sender_id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-start mb-3">
                                    <img src="<?php echo htmlspecialchars($conversation['sender_avatar'] ?: 'default-avatar.png'); ?>" 
                                         alt="Sender Avatar" 
                                         class="img-thumbnail rounded-circle me-3" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($conversation['sender_username']); ?></strong>
                                        <p><?php echo htmlspecialchars($conversation['last_message']); ?></p>
                                        <small><?php echo date('Y-m-d H:i', strtotime($conversation['last_message_time'])); ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.message-timeline {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 1em;
}
.message {
    padding: 10px;
    border-radius: 3px;
    margin-bottom: 3px;
}
.incoming {
    background-color: #1b2742;
}
.outgoing {
    background-color: #135436;
    text-align: left;
}
.list-group-item {
    border: 1px solid #ddd;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 5px;
}
a.text-light {
    color: #f8f9fa; /* Adjust as needed */
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Send message via AJAX
    $('#message-form').submit(function(e) {
        e.preventDefault();
        var message = $('#message-input').val();
        $.ajax({
            url: 'message.php?user_id=<?php echo $receiver_id; ?>',
            type: 'POST',
            data: { message: message },
            success: function(data) {
                $('#message-input').val('');
                loadMessages();
            }
        });
    });

    // Load new messages
    function loadMessages() {
        $.ajax({
            url: 'fetch_messages.php?user_id=<?php echo $receiver_id; ?>',
            success: function(data) {
                $('#message-container').html(data);
                scrollToBottom(); // Scroll to the bottom after loading messages
            }
        });
    }

    // Scroll to the bottom of the message container
    function scrollToBottom() {
        var messageContainer = $('#message-container');
        messageContainer.scrollTop(messageContainer[0].scrollHeight);
    }

    // Typing notification
    var typingTimeout;
    $('#message-input').on('input', function() {
        clearTimeout(typingTimeout);
        $.post('typing.php', { user_id: <?php echo $receiver_id; ?> });
        typingTimeout = setTimeout(stopTyping, 2000);
    });

    function stopTyping() {
        $.post('typing.php', { user_id: <?php echo $receiver_id; ?>, stop: true });
    }

    setInterval(loadMessages, 3000); // Load new messages every 3 seconds
    setInterval(checkTyping, 1000); // Check typing status every 1 second

    function checkTyping() {
        $.get('check_typing.php?user_id=<?php echo $receiver_id; ?>', function(data) {
            $('#typing-status').text(data);
        });
    }

    // Initial scroll to the bottom on page load
    scrollToBottom();
});
</script>

<?php include 'footer.php'; ?>
