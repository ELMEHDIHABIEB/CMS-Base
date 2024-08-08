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

// Update user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];
    $avatar = $_FILES['avatar'];

    // Handle avatar upload
    if ($avatar['size'] > 0) {
        $target_dir = "avatar/";
        $target_file = $target_dir . basename($avatar["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($avatar["tmp_name"]);

        if ($check !== false && $avatar["size"] <= 5242880 && ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg")) {
            // Check image resolution
            if ($check[0] <= 3000 && $check[1] <= 3000) {
                if (move_uploaded_file($avatar["tmp_name"], $target_file)) {
                    // Update avatar path in the database
                    $update_query = "UPDATE users SET avatar = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param('si', $target_file, $user_id);
                    $update_stmt->execute();
                } else {
                    $error_message = "Failed to upload avatar.";
                }
            } else {
                $error_message = "Image resolution exceeds 3000x3000 pixels.";
            }
        } else {
            $error_message = "Invalid file. Please upload a JPG, JPEG, or PNG file not exceeding 5MB.";
        }
    }

    // Update only if values have changed
    $update_query = "UPDATE users SET first_name = IF(first_name != ?, ?, first_name), email = IF(email != ?, ?, email), phone = IF(phone != ?, ?, phone), bio = IF(bio != ?, ?, bio) WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssssssssi', $first_name, $first_name, $email, $email, $phone, $phone, $bio, $bio, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        header('Location: profile.php');
        exit;
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main>
    <div class="card bg-dark text-light">
        <div class="card-body">
            <h4 class="card-title mb-4"><i class="bi bi-pencil-square"></i> Edit Profile</h4>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="edit_profile.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($profile_user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile_user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile_user['phone']); ?>">
                </div>
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($profile_user['bio']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</main>
