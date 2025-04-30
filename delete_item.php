<?php
require_once 'db.php';
require_once 'session.php';

// Require login
requireLogin();

// Get recipe ID from URL
$recipeId = $_GET['id'] ?? null;

if (!$recipeId) {
    setFlashMessage('error', 'Recipe ID is required');
    header("Location: dashboard.php");
    exit;
}

// Check if recipe exists and belongs to current user
try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipeId, getCurrentUserId()]);
    $recipe = $stmt->fetch();
    
    if (!$recipe) {
        setFlashMessage('error', 'Recipe not found or you do not have permission to delete it');
        header("Location: dashboard.php");
        exit;
    }
    
    // Delete image file if exists
    if ($recipe['image_path'] && file_exists($recipe['image_path'])) {
        unlink($recipe['image_path']);
    }
    
    // Delete recipe from database
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipeId, getCurrentUserId()]);
    
    setFlashMessage('success', 'Recipe deleted successfully!');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error deleting recipe: ' . $e->getMessage());
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>
