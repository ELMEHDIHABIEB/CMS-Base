<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $job_hours = $_POST['job_hours'];
    $salary = $_POST['salary'];
    $description = $_POST['description'];
    $phone = $_POST['phone'] ?? null;
    $email = $_POST['email'] ?? null;
    $posted_by = $_SESSION['user_id'];

    $sql = "INSERT INTO jobs (title, company, location, job_type, job_hours, salary, description, phone, email, posted_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Use 'ssssssssss' to specify 10 string parameters
    $stmt->bind_param('ssssssssss', $title, $company, $location, $job_type, $job_hours, $salary, $description, $phone, $email, $posted_by);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted job
        $job_id = $stmt->insert_id;
        header("Location: job.php?id=" . $job_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<?php include 'header.php'; ?>

<div class="container my-4">
    <h2 class="text-light mb-4">Post a Job</h2>
    <form method="post" class="bg-dark p-4 rounded shadow">
        <div class="form-group mb-3">
            <label for="title" class="text-light">Job Title:</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="company" class="text-light">Company Name:</label>
            <input type="text" id="company" name="company" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="location" class="text-light">Job Location:</label>
            <input type="text" id="location" name="location" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="job_type" class="text-light">Job Type:</label>
            <input type="text" id="job_type" name="job_type" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="job_hours" class="text-light">Job Hours:</label>
            <input type="text" id="job_hours" name="job_hours" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="salary" class="text-light">Salary:</label>
            <input type="text" id="salary" name="salary" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="text-light">Job Description:</label>
            <textarea id="description" name="description" class="form-control" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="phone" class="text-light">Company Phone (optional):</label>
            <input type="text" id="phone" name="phone" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="email" class="text-light">Company Email (optional):</label>
            <input type="email" id="email" name="email" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Post Job</button>
    </form>
</div>

<?php include 'footer.php'; ?>
