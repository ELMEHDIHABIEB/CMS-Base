<?php
session_start();
require 'config.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile_user = $result->fetch_assoc();
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main>
    <div class="card bg-dark text-light">
        <div class="card-body">
            <h4 class="card-title mb-4"><i class="bi bi-person-circle"></i> Profile</h4>
            <div class="profile-info">
                <img src="<?php echo htmlspecialchars($profile_user['avatar']); ?>" alt="Avatar" class="img-thumbnail" style="max-width: 150px;">
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($profile_user['first_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile_user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile_user['phone']); ?></p>
                <p><strong>Bio:</strong> <?php echo htmlspecialchars($profile_user['bio']); ?></p>
            </div>
        </div>
    </div>
</main>
