<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $logged_in_user = true;
    $username = $_SESSION['username'];
} else {
    $logged_in_user = false;
    $username = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'QuickIN'; ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
<div class="container">
<a class="navbar-brand" href="/">QuickIN</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link active" href="/profile.php">My Profile</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/message.php">Message</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/jobs.php">Job Listings</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/post.php">Post a Job</a>
</li>
</ul>
<ul class="navbar-nav ml-auto">

<li class="nav-item navbar-text">
<i class="bi bi-person-circle"></i> <strong></strong>
<a href="logout.php" class="btn btn-danger btn-sm ml-2">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</li>

<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>

</ul>
</div>
</div>
</nav>
