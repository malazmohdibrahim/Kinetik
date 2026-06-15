<?php
session_start();
require_once 'database/connection.php';

// Enforce active authentication session gatekeeping
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Systemic profile token baseline mapped dynamic from current user session
$customerId = $_SESSION['user_id']; 
$totalPortfolioValue = 0;
$allocatedCars = [];

// 1. OPERATION PORTFOLIO EVICTION: Securely strip item records from order_details
if (isset($_GET['remove'])) {
    $detailId = (int)$_GET['remove'];
    try {
        // Enforce secure customer validation scoping before executing raw row deletes
        $deleteStmt = $pdo->prepare("
            DELETE od FROM order_details od
            JOIN orders o ON od.order_id = o.id
            WHERE od.id = ? AND o.customer_id = ?
        ");
        $deleteStmt->execute([$detailId, $customerId]);
        
        header("Location: garage.php");
        exit();
    } catch (Exception $e) {
        die("Systemic tracking eviction failed: " . $e->getMessage());
    }
}

// 2. DATA ACQUISITION PROTOCOL: Extract tracking layers matching exact schema properties
try {
    $stmt = $pdo->prepare("
        SELECT 
            od.id AS tracking_detail_id,
            od.quantity,
            od.price_at_purchase,
            v.brand,
            v.model_name,
            v.main_image,
            v.category,
            v.horsepower,
            v.top_speed_kmh,
            v.id AS vehicle_real_id
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        JOIN vehicles v ON od.vehicle_id = v.id
        WHERE o.customer_id = ? AND o.status = 'Pending'
    ");
    $stmt->execute([$customerId]);
    $allocatedCars = $stmt->fetchAll();

    // Map mathematical asset values safely
    foreach ($allocatedCars as $car) {
        $totalPortfolioValue += ($car['price_at_purchase'] * $car['quantity']);
    }
} catch (Exception $e) {
    die("Relational matrix alignment layout compilation error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetik | My Garage </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span>.</span></a>
            <div class="nav-links">
                <a href="index.php">home</a>
                <a href="collection.php">Collection</a>
                <a href="garage.php" class="active">My Garage</a>
            </div>
        </div>
    </nav>

    <main class="container garage-layout-wrapper" style="margin-top: 40px; margin-bottom: 60px;">
        <header class="garage-header-block" style="margin-bottom: 35px;">
            <span class="category-tag">my garage</span>
            <h1 style="font-size: 36px; font-weight: 900; margin-top: 8px; letter-spacing: 1px; color: #fff;">MY Cars</h1>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">you have a good eye for strong performance cars .</p>
        </header>

        <?php if (empty($allocatedCars)): ?>
            <div class="glass-panel empty-garage-card" style="text-align: center; padding: 80px 40px; max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px;">
                <h3 style="font-size: 18px; font-weight: 800; letter-spacing: 1px; margin-bottom: 10px; color: #fff;">YOUR GARAGE IS EMPTY</h3>
                <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 30px;">Browse our collection to see if anything represents what ypure looking for.</p>
                <a href="collection.php" class="cta-primary" style="display: inline-block; padding: 12px 24px; background: #fff; color: #000; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; text-decoration: none; border-radius: 4px;">Browse Showroom Inventory</a>
            </div>
        <?php else: ?>
            <div class="garage-dashboard-grid" style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px; align-items: start;">
                
                <section class="garage-slots-column" style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($allocatedCars as $car): ?>
                        <div class="glass-panel garage-asset-card" style="display: grid; grid-template-columns: 240px 1fr; padding: 20px; gap: 24px; align-items: center; background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px;">
                            <div class="asset-image-box" style="width: 100%; height: 140px; background: #000; border-radius: 4px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <img src="<?php echo htmlspecialchars($car['main_image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="asset-details-box" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div>
                                    <span class="category-tag" style="font-size: 10px; font-weight: 800; color: var(--accent-red); letter-spacing: 1.5px; text-transform: uppercase;"><?php echo htmlspecialchars($car['category']); ?></span>
                                    <h2 style="font-size: 22px; font-weight: 900; margin-top: 6px; margin-bottom: 4px; color: #fff; letter-spacing: 0.5px;"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?></h2>
                                    <p style="font-size: 12px; color: var(--text-muted); font-family: monospace;"><?php echo htmlspecialchars($car['horsepower']); ?> BHP | V-Max: <?php echo htmlspecialchars($car['top_speed_kmh']); ?> km/h</p>
                                </div>
                                <div class="asset-value-actions" style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 16px;">
                                    <span class="asset-price-tag" style="font-size: 20px; font-weight: 800; color: #fff;">$<?php echo number_format($car['price_at_purchase']); ?></span>
                                    <div class="asset-interactive-links" style="display: flex; gap: 12px; align-items: center;">
                                        <a href="product.php?id=<?php echo $car['vehicle_real_id']; ?>" class="inspect-btn" style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #fff; text-decoration: none; border: 1px solid rgba(255, 255, 255, 0.15); padding: 6px 14px; border-radius: 4px; background: rgba(255, 255, 255, 0.02);">Inspect 360°</a>
                                        <a href="garage.php?remove=<?php echo $car['tracking_detail_id']; ?>" class="evict-btn" style="font-size: 11px; color: #ef4444; text-decoration: none; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="portfolio-summary-column">
                    <div class="glass-panel summary-sticky-card" style="padding: 30px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; position: sticky; top: 30px;">
                        <h3 style="font-size: 16px; font-weight: 900; letter-spacing: 1.5px; color: #fff; text-transform: uppercase;">METRICS</h3>
                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 15px 0;">
                        
                        <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 13px;">
                            <span style="color: var(--text-muted);">Total Value Assessed</span>
                            <span style="font-weight: bold; color: #fff;">$<?php echo number_format($totalPortfolioValue); ?></span>
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>