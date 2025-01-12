<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'configdata.php';
require_once 'article.php';
require_once 'models.php';

$database = new Database();
$db = $database->getConnection();
$category = new Category($db);
$categories = $category->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article = new Article($db);
    
    $articleData = [
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'author_id' => $_SESSION['user_id'],
        'image' => null
    ];
    
    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/';
        $image_name = uniqid() . '_' . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
            $articleData['image'] = $image_name;
        }
    }
    
    if ($article->create($articleData)) {
        // Handle categories
        if (!empty($_POST['categories'])) {
            foreach ($_POST['categories'] as $category_id) {
                $category->attachToArticle($article->id, $category_id);
            }
        }
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Title:</label>
            <input type="text" name="title" required>
        </div>
        
        <div>
            <label>Content:</label>
            <textarea name="content" required></textarea>
        </div>
        
        <div>
            <label>Image:</label>
            <input type="file" name="image">
        </div>
        
        <div>
            <label>Categories:</label>
            <?php foreach($categories as $category): ?>
                <label>
                    <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </label>
            <?php endforeach; ?>
        </div>
        
        <button type="submit">Create Article</button>
    </form>
</body>
</html>
