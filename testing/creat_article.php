<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$article = new Article($pdo);
$category = new Category($pdo);
$error = '';
$success = '';

// Get all categories for the dropdown
$categories = $category->read();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleData = [
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'user_id' => $_SESSION['user_id'],
        'category_id' => $_POST['category_id']
    ];

    try {
        if ($article->create($articleData)) {
            $success = 'Article created successfully!';
        }
    } catch (Exception $e) {
        $error = 'Failed to create article. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        textarea { height: 200px; }
        .error { color: red; }
        .success { color: green; }
        .nav { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="nav">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | 
        <a href="logout.php">Logout</a>
    </div>

    <h2>Create New Article</h2>
    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category_id" id="category" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>
        </div>

        <button type="submit">Create Article</button>
    </form>
</body>
</html>