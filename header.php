<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Quick Job'; ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
<div class="container">
<a class="navbar-brand" href="/">Quick Job</a>
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
<span class="navbar-text ml-auto">
<i class="bi bi-box-arrow-right"></i> Logout</a>';
} else {
echo '<a class="btn btn-outline-light ml-3" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>';
}
?>
</span>
</div>
</div>
</nav>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3oWn8v8n8sV1N0R5rI2H3p0I5I3BdGRQ2IBuWc11ouK4F" crossorigin="anonymous" type="57a9bf916a3471b7a63de4ae-text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UOaGq7pS2w5V7H4aEzjo8fs5YEXshg5l1qq7S1R01dft9zBv1mkxJ57T0dgnp" crossorigin="anonymous" type="57a9bf916a3471b7a63de4ae-text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfyyHQ5T3kWcb5s6wBDi+YIMZI6qqXcbtGZPTOBYlTL2W2wWc1T9bEXsY4d2Fz" crossorigin="anonymous" type="57a9bf916a3471b7a63de4ae-text/javascript"></script>
<script src="/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="ca7dd37ad5ca87b160639752-|49" defer type="57a9bf916a3471b7a63de4ae-text/javascript"></script>
<script src="/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="57a9bf916a3471b7a63de4ae-|49" defer></script></body>
</html>
