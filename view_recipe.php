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

// Get recipe data
try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipeId, getCurrentUserId()]);
    $recipe = $stmt->fetch();
    
    if (!$recipe) {
        setFlashMessage('error', 'Recipe not found or you do not have permission to view it');
        header("Location: dashboard.php");
        exit;
    }
} catch (PDOException $e) {
    setFlashMessage('error', 'Error fetching recipe: ' . $e->getMessage());
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['name']); ?> - Recipe Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h1><?php echo htmlspecialchars($recipe['name']); ?></h1>
                    <div class="btn-group">
                        <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary">Edit Recipe</a>
                        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
                    </div>
                </div>
                
                <div class="recipe-detail">
                    <div class="recipe-detail-image">
                        <?php if (!empty($recipe['image_path'])): ?>
                            <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">No Image Available</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="recipe-detail-content">
                        <section class="recipe-section">
                            <h2>Ingredients</h2>
                            <div class="ingredients-list">
                                <?php 
                                $ingredients = explode("\n", $recipe['ingredients']);
                                foreach ($ingredients as $ingredient): 
                                    $ingredient = trim($ingredient);
                                    if (!empty($ingredient)):
                                ?>
                                    <div class="ingredient-item">
                                        <span class="ingredient-bullet">•</span>
                                        <span><?php echo htmlspecialchars($ingredient); ?></span>
                                    </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </section>
                        
                        <section class="recipe-section">
                            <h2>Method</h2>
                            <div class="method-content">
                                <?php 
                                $steps = explode("\n", $recipe['method']);
                                $stepCount = 1;
                                foreach ($steps as $step): 
                                    $step = trim($step);
                                    if (!empty($step)):
                                ?>
                                    <div class="method-step">
                                        <div class="step-number"><?php echo $stepCount++; ?></div>
                                        <div class="step-text"><?php echo htmlspecialchars($step); ?></div>
                                    </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </section>
                        
                        <div class="recipe-meta">
                            <p>Added on: <?php echo date('F j, Y', strtotime($recipe['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
