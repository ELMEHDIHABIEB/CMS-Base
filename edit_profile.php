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
    $field = $_POST['field'];
    $value = $_POST['value'];
    $user_id = $_SESSION['user_id'];

    if ($field == 'avatar' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $value = $target_file;
        }
    }

    $sql = "UPDATE users SET $field=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $value, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: profile.php?username=" . $_SESSION['username']);
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
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form method="post">
            <input type="hidden" name="field" value="full_name">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="value" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            <button type="submit">Edit</button>
        </form>

        <form method="post">
            <input type="hidden" name="field" value="address">
            <label for="address">Address</label>
            <input type="text" id="address" name="value" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            <button type="submit">Edit</button>
        </form>

        <form method="post">
            <input type="hidden" name="field" value="gender">
            <label for="gender">Gender</label>
            <select id="gender" name="value" required>
                <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
            <button type="submit">Edit</button>
        </form>

        <form method="post">
            <input type="hidden" name="field" value="bio">
            <label for="bio">Bio</label>
            <textarea id="bio" name="value" required><?php echo htmlspecialchars($user['bio']); ?></textarea>
            <button type="submit">Edit</button>
        </form>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="field" value="avatar">
            <label for="avatar">Avatar</label>
            <input type="file" id="avatar" name="avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" width="100">
            <?php endif; ?>
            <button type="submit">Edit</button>
        </form>

        <p><a href="profile.php?username=<?php echo htmlspecialchars($user['username']); ?>">Back to Profile</a></p>
    </div>
</body>
</html>
