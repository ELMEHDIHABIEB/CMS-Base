<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include necessary files
require_once 'config.php';

// Handle article submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];

    $sql = "INSERT INTO articles (title, content, author_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $title, $content, $author_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch articles
$sql = "SELECT id, title, content FROM articles WHERE author_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <h3>Add Article</h3>
        <form method="post">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="content" placeholder="Content" required></textarea>
            <button type="submit">Add Article</button>
        </form>
        <h3>Your Articles</h3>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h4><?php echo $article['title']; ?></h4>
                <p><?php echo $article['content']; ?></p>
                <!-- Add edit/delete buttons as needed -->
            </div>
        <?php endforeach; ?>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
