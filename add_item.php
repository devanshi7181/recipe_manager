<?php
require_once 'db.php';
require_once 'session.php';

// Require login
requireLogin();

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $ingredients = $_POST['ingredients'] ?? '';
    $method = $_POST['method'] ?? '';
    
    if (empty($name) || empty($ingredients) || empty($method)) {
        $error = "Please fill in all required fields";
    } else {
        try {
            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate unique filename
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $newFilename;
                
                // Check if it's a valid image
                $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $validTypes)) {
                    $error = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
                } else {
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = $uploadPath;
                    } else {
                        $error = "Failed to upload image";
                    }
                }
            }
            
            if (empty($error)) {
                // Insert recipe into database
                $stmt = $pdo->prepare("INSERT INTO recipes (user_id, name, ingredients, method, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([getCurrentUserId(), $name, $ingredients, $method, $imagePath]);
                
                setFlashMessage('success', 'Recipe added successfully!');
                header("Location: dashboard.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe - Recipe Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h1>Add New Recipe</h1>
                    <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="form-card">
                    <form method="POST" action="add_item.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Recipe Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea id="ingredients" name="ingredients" rows="5" required placeholder="Enter ingredients, one per line"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="method">Method</label>
                            <textarea id="method" name="method" rows="8" required placeholder="Enter cooking instructions"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Recipe Image</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="image" name="image" accept="image/*">
                                <div class="file-preview">
                                    <img id="image-preview" src="#" alt="Preview" style="display: none;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Recipe</button>
                            <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>
