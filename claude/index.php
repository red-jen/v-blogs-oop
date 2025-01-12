<?php
session_start();
require_once 'configdata.php';
require_once 'models.php';

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

$articles = $article->getWithDetails();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="create-article.php">Create Article</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <main>
        <?php foreach($articles as $article): ?>
            <article>
                <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                <p>By <?php echo htmlspecialchars($article['author_name']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                <div>
                    <span>Likes: <?php echo $article['likes_count']; ?></span>
                    <a href="article.php?id=<?php echo $article['id']; ?>">Read More</a>
                </div>
            </article>
        <?php endforeach; ?>
    </main>
</body>
</html>
