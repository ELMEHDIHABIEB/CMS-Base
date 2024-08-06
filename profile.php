<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include necessary files
require_once 'config.php';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $bio = $_POST['bio'];
    $avatar = '';

    // Handle file upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $avatar = $target_file;
        }
    }

    $user_id = $_SESSION['user_id'];
    $sql = "UPDATE users SET full_name=?, address=?, gender=?, bio=?, avatar=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssi', $full_name, $address, $gender, $bio, $avatar, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: profile.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch user profile
$sql = "SELECT username, full_name, address, gender, bio, avatar FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>

            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>

            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" required><?php echo htmlspecialchars($user['bio']); ?></textarea>

            <label for="avatar">Avatar</label>
            <input type="file" id="avatar" name="avatar">

            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" width="100">
            <?php endif; ?>

            <button type="submit">Update Profile</button>
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
