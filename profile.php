<?php
require_once 'config.php';
session_start();
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_user_id = $_SESSION['user_id'];
// Fetch profile data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('i', $logged_in_user_id);
$stmt->execute();
$profile_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
        body {
            background-color: #121212;
            color: #fff;
        }
        .card {
            background-color: #1E1E1E;
            border: none;
            color: #fff;
        }
       .text-success {
    color: #d0e1ed!important;
}
        #sidebar {
            position: fixed;
            left: 0;
            height: 100vh;
            width: 250px;
            transition: all 0.3s;
        }
        #sidebar .nav-link {
            padding: 15px 20px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        #sidebar .nav-link:hover {
            background-color: #28a745;
        }
        #sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        main {
            margin-left: 250px;
            transition: all 0.3s;
        }
        @media (max-width: 768px) {
            #sidebar {
                width: 80px;
            }
            #sidebar .nav-label {
                display: none;
            }
            #sidebar .nav-link {
                justify-content: center;
                padding: 15px;
            }
            #sidebar .nav-link i {
                margin-right: 0;
            }
            main {
                margin-left: 80px;
            }
        }
        .user-avatar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<nav id="sidebar" class="bg-dark">
<div class="sidebar-content">
<ul class="nav flex-column">
<li class="nav-item">
<a class="nav-link text-white" href="dashboard.php">
<i class="bi bi-house-door-fill text-success"></i>
<span class="nav-label">Dashboard</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="profile.php">
<i class="bi bi-person-fill text-success"></i>
<span class="nav-label">Profile</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="message.php">
<i class="bi bi-chat-dots-fill text-success"></i>
<span class="nav-label">Messages</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="privacy.php">
<i class="bi bi-shield-lock-fill text-success"></i>
<span class="nav-label">Privacy</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="settings.php">
<i class="bi bi-gear-fill text-success"></i>
<span class="nav-label">Settings</span>
</a>
</li>
</ul>
</div>
</nav>
<main>
<div class="container my-4">
<div class="row gutters">
<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
<div class="card h-100">
<div class="card-body">
<div class="account-settings">
<div class="user-profile">
<div class="user-avatar">
';
} else {
echo '<img src="default-avatar.png" alt="Default Avatar">';
}
?>
</div>
<h5 class="user-name"></h5>
<h6 class="user-email"></h6>
</div>
<div class="about mt-4">
<h5>Bio</h5>
<p></p>
</div>
<a href="edit_profile.php" class="btn btn-success mt-3">
<i class="bi bi-pencil-fill"></i> Edit Profile
</a>
</div>
</div>
</div>
</div>
<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
<div class="card h-100">
<div class="card-body">
<div class="row gutters">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
<h6 class="mb-3 text-primary">Personal Details</h6>
</div>
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
<div class="form-group mb-3">
<label for="fullName" class="mb-1"><i class="bi bi-person-fill text-success"></i> Full Name</label>
<p></p>
</div>
</div>
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
<div class="form-group mb-3">
<label for="eMail" class="mb-1"><i class="bi bi-envelope-fill text-success"></i> Email</label>
<p></p>
</div>
</div>
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
<div class="form-group mb-3">
<label for="phone" class="mb-1"><i class="bi bi-telephone-fill text-success"></i> Phone</label>
<p></p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
' . htmlspecialchars($_SESSION['success_message']) . '</div>';
unset($_SESSION['success_message']);
}
?>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" type="37d94cb818f73f3f972a37c7-text/javascript"></script>
<script src="/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="37d94cb818f73f3f972a37c7-|49" defer></script></body>
</html>
