<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle CV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    $allowed_types = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    $max_file_size = 8 * 1024 * 1024; // 8MB

    // Check file type and size
    if (in_array($file['type'], $allowed_types) && $file_size <= $max_file_size && $file_error == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($file_name);

        if (move_uploaded_file($file_tmp, $upload_file)) {
            // Store the file information in the database
            $sql = "INSERT INTO user_cvs (user_id, file_name, file_path) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iss', $user_id, $file_name, $upload_file);
            $stmt->execute();
            $stmt->close();

            $success_message = "CV uploaded successfully.";
        } else {
            $error_message = "Error uploading the file.";
        }
    } else {
        $error_message = "Invalid file type or size. Please upload a PDF or DOC/DOCX file up to 8MB.";
    }
}

// Handle CV download
if (isset($_GET['download'])) {
    $file_id = $_GET['download'];
    $sql = "SELECT file_name, file_path FROM user_cvs WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $file_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($file_name, $file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_name && $file_path) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        readfile($file_path);
        exit();
    }
}

// Handle CV delete
if (isset($_GET['delete'])) {
    $file_id = $_GET['delete'];
    $sql = "SELECT file_path FROM user_cvs WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $file_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path && unlink($file_path)) {
        $sql = "DELETE FROM user_cvs WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $file_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $success_message = "CV deleted successfully.";
    } else {
        $error_message = "Error deleting the CV.";
    }
}

// Fetch user's uploaded CVs
$sql = "SELECT id, file_name, file_path FROM user_cvs WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($id, $file_name, $file_path);
$user_cvs = array();
while ($stmt->fetch()) {
    $user_cvs[] = array(
        'id' => $id,
        'file_name' => $file_name,
        'file_path' => $file_path
    );
}
$stmt->close();
$conn->close();
?>

<?php include 'header.php'; ?><?php include 'sidebar.php'; ?>
<main>
<div class="card bg-dark text-light">
<div class="card-body">
<h4 class="card-title mb-4"><i class="bi bi-file-person"></i> Upload CV</h4>
<div class="container my-5">

<?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="cv_file" class="form-label">Choose CV file</label>
                <input type="file" class="form-control" id="cv_file" name="cv_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <h2>Your Uploaded CVs</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_cvs as $cv): ?>
                    <tr>
                        <td><?php echo $cv['file_name']; ?></td>
                        <td>
                            <a href="?download=<?php echo $cv['id']; ?>" class="btn btn-primary btn-sm">Download</a>
                            <a href="?delete=<?php echo $cv['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
