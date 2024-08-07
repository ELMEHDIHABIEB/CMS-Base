<?php
session_start();
require_once 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$flash_message = '';
$username = '';
$current_password = '';
$new_password = '';
$confirm_password = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_SESSION['user_id']);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Check current password
    if (!password_verify($current_password, $user['password'])) {
        $flash_message = 'Error: Current password is incorrect.';
    } elseif ($new_password !== $confirm_password) {
        $flash_message = 'Error: New passwords do not match.';
    } else {
        // Update username and/or password
        $sql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $username, $new_password_hash, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $flash_message = 'Success: Your password has been updated.';
        } else {
            $flash_message = 'Error: Failed to update profile.';
        }
        $stmt->close();
    }
}

// Fetch user information
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <style>
      /* Sidebar Styles */
      #sidebar {
          height: 100vh;
          position: fixed;
          left: 0;
          z-index: 100;
          background-color: #343a40;
          width: 250px;
          transition: width 0.3s;
      }
      .sidebar-content {
          padding: 1rem;
      }
      .sidebar-content h4 {
          margin-bottom: 1rem;
          color: white;
      }
      /* Navigation Link Styles */
      .nav-link {
          padding: 0.75rem;
          border-radius: 0.25rem;
          transition: background-color 0.3s, padding 0.3s;
          color: white;
          display: flex;
          align-items: center;
      }
      .nav-link:hover {
          background-color: #495057;
      }
      .nav-link i {
          font-size: 1.25rem;
          margin-right: 0.3rem;
      }
      /* Hide text labels on small screens */
      .nav-link .nav-label {
          display: inline;
      }
      @media (max-width: 767px) {
          #sidebar {
              width: 60px;
              overflow-x: hidden;
          }
          .sidebar-content h4 {
              display: none;
          }
          .nav-link {
              padding: 0.75rem;
              justify-content: center;
              text-align: center;
          }
          .nav-link .nav-label {
              display: none;
          }
          .nav-link i {
              font-size: 1.5rem;
          }
      }
      /* Main Content Styles */
      main {
          margin-left: 250px;
          padding: 2rem;
      }
      @media (max-width: 767px) {
          main {
              margin-left: 60px;
          }
      }
  </style>
</head>
<body>
<nav id="sidebar" class="d-none d-lg-block">
  <div class="sidebar-content">
    <h4>Navigation</h4>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Settings</a>
      </li>
    </ul>
  </div>
</nav>
<main>
  <div class="container my-4">
    <?php if (!empty($flash_message)) : ?>
      <div class="<?php echo strpos($flash_message, 'Error') !== false ? 'alert alert-danger' : 'alert alert-success'; ?>" role="alert">
        <?php echo $flash_message; ?>
      </div>
    <?php endif; ?>
    <div class="card bg-dark text-light">
      <div class="card-body">
        <h3 class="card-title mb-4"><i class="bi bi-fingerprint"></i> Update Password</h3>
        <form method="post">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control bg-secondary text-white" id="username" name="username" value="<?php echo htmlspecialchars($username ?: $user['username']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control bg-secondary text-white" id="current_password" name="current_password" required>
          </div>
          <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control bg-secondary text-white" id="new_password" name="new_password">
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control bg-secondary text-white" id="confirm_password" name="confirm_password">
          </div>
          <button type="submit" class="btn btn-success">Update</button>
        </form>
      </div>
    </div>
  </div>
</main>

<script src="<?php echo base_url('assets/js/script.js'); ?>"></script>

</body>
</html>
