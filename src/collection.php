<?php
// 1. Establish the unified secure data layer link
require_once 'database/connection.php';

// 2. Extract dynamic filter and search parameters from the request query string
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$brand    = isset($_GET['brand']) ? trim($_GET['brand']) : '';

// 3. Construct the base relational query pattern
$query = "SELECT * FROM vehicles WHERE 1=1";
$params = [];

// Apply Search Text Matching (Checks Brand or Model Designation)
if ($search !== '') {
    $query .= " AND (brand LIKE :search OR model_name LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

// Apply Explicit Category Filters
if ($category !== '') {
    $query .= " AND category = :category";
    $params['category'] = $category;
}

// Apply Explicit Brand Filters
if ($brand !== '') {
    $query .= " AND brand = :brand";
    $params['brand'] = $brand;
}

// Order logically by the most recently deployed vehicles
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $vehicles = [];
}

// Fetch all available unique brands and categories from the database for the dynamic filter menus
try {
    $brandStmt = $pdo->query("SELECT DISTINCT brand FROM vehicles ORDER BY brand ASC");
    $availableBrands = $brandStmt->fetchAll(PDO::FETCH_COLUMN);

    $catStmt = $pdo->query("SELECT DISTINCT category FROM vehicles ORDER BY category ASC");
    $availableCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $availableBrands = [];
    $availableCategories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Fleet Fleet Inventory | Kinetik Collection</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar glass-panel">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span class="red-dot">.</span></a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="collection.php" class="active">Collection</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="cart.php">Cart</a>
                <a href="profile.php">Profile</a>
                <a href="admin.php">Admin</a>
            </div>
        </div>
    </nav>

    <main class="container">
        
        <header class="hero-section glass-panel">
            <h1>THE KINETIK <span class="text-accent">FLEET</span></h1>
            <p>Inspect our comprehensive inventory of performance-engineered supercars currently marshaled in Kigali.</p>
        </header>

        <section class="filter-wrapper glass-panel" style="padding: 25px; margin-bottom: 40px;">
            <form action="collection.php" method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px;">Search Inventory</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g. Huracán or Porsche..." style="width: 100%; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); padding: 12px; color: #fff; border-radius: 6px;">
                </div>

                <div style="min-width: 180px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px;">Classification</label>
                    <select name="category" style="width: 100%; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); padding: 12px; color: #fff; border-radius: 6px;">
                        <option value="">All Categories</option>
                        <?php foreach ($availableCategories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="min-width: 180px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px;">Marque / Brand</label>
                    <select name="brand" style="width: 100%; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); padding: 12px; color: #fff; border-radius: 6px;">
                        <option value="">All Brands</option>
                        <?php foreach ($availableBrands as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $brand === $b ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="accent-button">Apply Filters</button>
                    <?php if ($search !== '' || $category !== '' || $brand !== ''): ?>
                        <a href="collection.php" class="btn-secondary" style="display: inline-block; padding: 12px 15px; border: 1px solid var(--glass-border); border-radius: 6px; text-decoration: none; text-align: center; font-size: 12px; text-transform: uppercase;">Clear</a>
                    <?php endif; ?>
                </div>

            </form>
        </section>

        <section class="showroom-grid">
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="car-card glass-panel">
                        
                        <div class="car-image-placeholder" style="background-image: url('<?php echo htmlspecialchars($vehicle['main_image']); ?>'); background-size: cover; background-position: center;">
                            <?php if (empty($vehicle['main_image'])): ?>
                                <div style="width:100%; height:100%; background: rgba(255,255,255,0.02);"></div>
                            <?php endif; ?>
                        </div>

                        <div class="car-details">
                            <span class="category-tag"><?php echo htmlspecialchars($vehicle['category']); ?></span>
                            <h3 style="margin: 10px 0;"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model_name']); ?></h3>
                            <p class="price">$<?php echo number_format($vehicle['price']); ?></p>
                            
                            <div style="font-size: 12px; color: #9ca3af; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
                                <span>🚀 <?php echo htmlspecialchars($vehicle['horsepower']); ?> HP</span>
                                <span>🏁 <?php echo htmlspecialchars($vehicle['top_speed_kmh']); ?> km/h</span>
                            </div>

                            <div class="card-actions" style="justify-content: flex-start;">
                                <a href="product.php?id=<?php echo $vehicle['id']; ?>" class="accent-button" style="width: 100%; text-align: center; text-decoration: none;">Show Details</a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="glass-panel" style="grid-column: 1 / -1; padding: 60px; text-align: center;">
                    <p style="color: #9ca3af; margin: 0; font-size: 15px;">No luxury units matched your chosen performance parameters.</p>
                    <a href="collection.php" style="color: var(--accent-red); display: inline-block; margin-top: 15px; font-weight: 600; text-decoration: none;">Reset Search Catalog</a>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <footer class="footer glass-panel">
        <div class="footer-container">
            <p>&copy; 2026 Kinetik Luxury Motors. All Rights Reserved.</p>
            <p class="academic-credit">UNILAK Final Project | E-Commerce & Web Applications</p>
        </div>
    </footer>

</body>
</html>