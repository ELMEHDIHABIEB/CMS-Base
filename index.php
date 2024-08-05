<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SimpleCMS Articles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
        }
        nav {
            background-color: #444;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .article {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
            font-size: 24px;
        }
        .article .content {
            margin-top: 10px;
        }
        .article .info {
            font-size: 0.8em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SimpleCMS Articles</h1>
    </div>

    <nav>
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
    </nav>

    <div class="container">

        <?php
        // Include configuration file
        include 'config.php';

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

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> SimpleCMS</p>
    </div>
</body>
</html>
