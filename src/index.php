<?php
// 1. Establish the unified secure database connection link
require_once 'database/connection.php';

// 2. Query the data layer to fetch a restricted subset of high-performance vehicles for the home showcase
try {
    // Selects the first two registered models to act as the featured showroom variants
    $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at ASC LIMIT 2");
    $featuredCars = $stmt->fetchAll();
} catch (Exception $e) {
    // Graceful baseline fallback array if the tables are temporarily locked or building
    $featuredCars = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetik | Luxury Automotive Showroom Kigali</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span>.</span></a>
            <div class="nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="collection.php">Collection</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="garage.php">my</a>
                <a href="login.php">Profile</a>
                <a href="admin.php">Admin</a>
            </div>
        </div>
    </nav>

    <header class="container">
        <div class="hero-content-clean">
            <h1>KINETIK<span>.</span></h1>
            <p>Rwanda's premier destination for high-performance exotics. We source, marshal, and support elite international supercars directly from our staging center in Kigali.</p>
            <div class="hero-actions">
                <a href="collection.php" class="cta-primary">Browse Inventory</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="featured-section">
            <div class="section-header">
                <h2>FEATURED CARS</h2>
                <p>A handpicked selection of elite machinery currently available for acquisition.</p>
            </div>

            <div class="showroom-grid-compact">
                <?php if (!empty($featuredCars)): ?>
                    <?php foreach ($featuredCars as $car): ?>
                        <div class="car-card-compact glass-panel">
                            
                            <div class="car-image-container-compact">
                                <img src="<?php echo htmlspecialchars($car['main_image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?>">
                            </div>

                            <div class="car-details-compact">
                                <span class="category-tag"><?php echo htmlspecialchars($car['category']); ?></span>
                                <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?></h3>
                                <p class="price">$<?php echo number_format($car['price']); ?></p>
                                
                                <div class="specs-summary-compact">
                                    <span><?php echo htmlspecialchars($car['horsepower']); ?> HP</span>
                                    <span><?php echo htmlspecialchars($car['top_speed_kmh']); ?> km/h</span>
                                </div>

                                <div class="card-actions-compact">
                                    <a href="product.php?id=<?php echo $car['id']; ?>" class="accent-button-compact">Show Details</a>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results glass-panel">
                        <p>No featured vehicles currently synchronized in the primary showroom display.</p>
                        <a href="collection.php">View Main Inventory Database</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="stats-container glass-panel">
            <div class="stat-item">
                <span class="stat-number">04</span>
                <span class="stat-label">Elite Brands</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">2,600+</span>
                <span class="stat-label">Horsepower Marshaled</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Track Certified</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">Kigali</span>
                <span class="stat-label">Staging Hub</span>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2026 Kinetik Luxury Motors. All Rights Reserved.</p>
            <p class="academic-credit">UNILAK Final Project | E-Commerce & Web Applications</p>
        </div>
    </footer>

</body>
</html>