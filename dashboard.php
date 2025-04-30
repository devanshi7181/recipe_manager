<?php
require_once 'db.php';
require_once 'session.php';

// Require login
requireLogin();

// Get all recipes for the current user
try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([getCurrentUserId()]);
    $recipes = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlashMessage('error', 'Error fetching recipes: ' . $e->getMessage());
    $recipes = [];
}

// Get flash message
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Recipe Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h1>My Recipes</h1>
                    <a href="add_item.php" class="btn btn-primary">Add New Recipe</a>
                </div>
                
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($recipes)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📝</div>
                        <h2>No recipes yet</h2>
                        <p>Start by adding your first recipe!</p>
                        <a href="add_item.php" class="btn btn-primary">Add Recipe</a>
                    </div>
                <?php else: ?>
                    <div class="recipe-grid">
                        <?php foreach ($recipes as $recipe): ?>
                            <div class="recipe-card">
                                <div class="recipe-image">
                                    <?php if (!empty($recipe['image_path'])): ?>
                                        <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="recipe-content">
                                    <h3><?php echo htmlspecialchars($recipe['name']); ?></h3>
                                    <div class="recipe-actions">
                                        <a href="view_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                        <a href="delete_item.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
