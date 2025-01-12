<?php
// Create instances of the classes

// require_once 'BaseCrud.php';
// require_once 'like.php';
// require_once 'article.php';  // If you have an Article class
require_once 'user.php';  // If you have a User class
// include('comment.php');
// include('category.php');

$user = new User();
$article = new Article();
$comment = new Comment();
// $category = new Category();
$like = new Like();

// Register a new user
$userData = [
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => 'password123',
];
$user->create($userData);

// Create a new article
$articleData = [
    'title' => 'Introduction to OOP',
    'content' => 'Object-Oriented Programming is a programming paradigm...',
    'author_id' => 1,
];
$article->create($articleData);

// Add a comment to the article
$commentData = [
    'content' => 'Great article!',
    'article_id' => 1,
    'author_name' => 'Guest',
    'author_email' => 'guest@example.com',
];
$comment->create($commentData);

// Fetch all articles with details
$articles = $article->getWithDetails();
print_r($articles);

// Fetch comments for the article
$comments = $comment->getByArticle(1);
print_r($comments);

// Toggle a like on the article
$like->toggle(1, 1); // Like article ID 1 by user ID 1

// Delete the article (and related records)
$article->delete(1);