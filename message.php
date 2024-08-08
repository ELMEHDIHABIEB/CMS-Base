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
       
         <?php include 'header.php'; ?><?php include 'sidebar.php'; ?>
<main>
<div class="card bg-dark text-light">
<div class="card-body">
<h4 class="card-title mb-4"><i class="bi bi-inbox"></i> Inbox</h4>
  
<div class="card-header text-center">
<i class="bi bi-chat-left-text-fill"></i> Messages
</div>
<div class="card-body">
<p>No messages found.</p>
</div>
</div>
</div>

</main>




