<header class="app-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="dashboard.php">
                    <span class="logo-icon">🍽️</span>
                    <span class="logo-text">Recipe Manager</span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="add_item.php">Add Recipe</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                </div>
                <a href="logout.php" class="btn btn-sm btn-outline">Logout</a>
            </div>
        </div>
    </div>
</header>
