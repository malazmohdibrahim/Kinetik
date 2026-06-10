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
    <title>Fleet Inventory | Kinetik Collection</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span>.</span></a>
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
        
        <header class="collection-header-clean">
            <h1>our <span>collection</span></h1>
            <p>Inspect our comprehensive fleet of performance-engineered supercars currently marshaled in Kigali.</p>
        </header>

        <section class="filter-wrapper-clean">
            <form action="collection.php" method="GET">
                
                <div class="form-group">
                    <label>Search Inventory</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g. Huracán or Porsche...">
                </div>

                <div class="form-group">
                    <label>Classification</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($availableCategories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Marque / Brand</label>
                    <select name="brand">
                        <option value="">All Brands</option>
                        <?php foreach ($availableBrands as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $brand === $b ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="accent-button">Apply Filters</button>
                    <?php if ($search !== '' || $category !== '' || $brand !== ''): ?>
                        <a href="collection.php" class="btn-secondary">Clear</a>
                    <?php endif; ?>
                </div>

            </form>
        </section>

        <section class="showroom-grid">
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="car-card glass-panel">
                        
                        <div class="car-image-container">
                            <img src="<?php echo htmlspecialchars($vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model_name']); ?>">
                        </div>

                        <div class="car-details">
                            <span class="category-tag"><?php echo htmlspecialchars($vehicle['category']); ?></span>
                            <h3><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model_name']); ?></h3>
                            <p class="price">$<?php echo number_format($vehicle['price']); ?></p>
                            
                            <div class="specs-summary">
                                <span class="spec-hp">Horsepower: <?php echo htmlspecialchars($vehicle['horsepower']); ?> HP</span>
                                <span class="spec-speed">Top Speed: <?php echo htmlspecialchars($vehicle['top_speed_kmh']); ?> km/h</span>
                            </div>

                            <p class="car-description"><?php echo htmlspecialchars($vehicle['description']); ?></p>

                            <div class="card-actions">
                                <a href="product.php?id=<?php echo $vehicle['id']; ?>" class="accent-button">Show Details</a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results glass-panel">
                    <p>No luxury units matched your chosen performance parameters.</p>
                    <a href="collection.php">Reset Search Catalog</a>
                </div>
            <?php endif; ?>
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