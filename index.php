<?php
// Include configuration file
include 'config.php';
?>

<?php include 'header.php'; ?>

<div class="container">

    <?php
    // Query to fetch articles with user information
    $query = "SELECT articles.title, articles.content, articles.created_at, users.username
              FROM articles
              INNER JOIN users ON articles.author_id = users.id
              ORDER BY articles.created_at DESC";

    // Execute query
    $result = $conn->query($query);

    // Check if there are articles
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<div class="article">';
            echo '<h2>' . htmlspecialchars($row['title']) . '</h2>';
            echo '<div class="content">' . htmlspecialchars($row['content']) . '</div>';
            echo '<div class="info">';
            echo 'Posted by ' . htmlspecialchars($row['username']) . ' on ' . htmlspecialchars($row['created_at']);
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No articles found.</p>';
    }

    // Close result set
    $result->free();
    ?>

</div>

<?php include 'footer.php'; ?>
