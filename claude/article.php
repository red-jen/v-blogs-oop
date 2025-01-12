<?php
session_start();
require_once 'configdata.php';
require_once 'models.php';

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$article->id = $_GET['id'];
$articleData = $article->getOne($_GET['id']);

$comment = new Comment($db);
$comments = $comment->getByArticle($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $like = new Like($db);
        $like->toggle($_GET['id'], $_SESSION['user_id']);
        header("Location: article.php?id=" . $_GET['id']);
        exit;
    }
    
    if (isset($_POST['comment'])) {
        $commentData = [
            'content' => $_POST['content'],
            'article_id' => $_GET['id'],
            'author_name' => isset($_SESSION['username']) ? null : $_POST['name'],
            'author_email' => isset($_SESSION['username']) ? null : $_POST['email'],
            'author_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];
        
        $comment->create($commentData);
        header("Location: article.php?id=" . $_GET['id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($articleData['title']); ?></title>
</head>
<body>
    <article>
        <h1><?php echo htmlspecialchars($articleData['title']); ?></h1>
        <?php if($articleData['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($articleData['image']); ?>" alt="Article image">
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($articleData['content'])); ?></p>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <button type="submit" name="like">Like</button>
            </form>
        <?php endif; ?>
    </article>
    
    <section class="comments">
        <h2>Comments</h2>
        <form method="POST">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <div>
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
            <?php endif; ?>
            
            <div>
                <label>Comment:</label>
                <textarea name="content" required></textarea>
            </div>
            
            <button type="submit" name="comment">Add Comment</button>
        </form>
        
        <?php foreach($comments as $comment): ?>
            <div class="comment">
                <p><?php echo htmlspecialchars($comment['commenter_name']); ?> says:</p>
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            </div>
        <?php endforeach; ?>
    </section>
</body>
</html>