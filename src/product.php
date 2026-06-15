<?php
session_start();
require_once 'database/connection.php';

// 1. Context initialization parameters
$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); // Enforce strict true/false baseline

// Fetch the current vehicle profile data for display
try {
    $vehicleQuery = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? LIMIT 1");
    $vehicleQuery->execute([$vehicleId]);
    $car = $vehicleQuery->fetch();
    
    // Fallback if vehicle doesn't exist
    if (!$car) {
        die("Requested exotic asset could not be located in the Kigali hub database.");
    }
} catch (Exception $e) {
    die("Database communication error: " . $e->getMessage());
}

// 2. DATABASE MECHANICS: Intercept submission and write directly into order_details schema
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'stage_asset') {
    
    // Hard check: Bounces requests back to login page immediately if user status evaluates to false
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit();
    }

    // Capture explicit ID mapping now that authentication state is proven true
    $customerId = $_SESSION['user_id']; 

    try {
        $pdo->beginTransaction();

        // Locate or provision an active draft tracking token wrapper inside orders table using customer_id
        $orderQuery = $pdo->prepare("SELECT id FROM orders WHERE customer_id = ? AND status = 'Pending' LIMIT 1");
        $orderQuery->execute([$customerId]);
        $activeOrder = $orderQuery->fetch();

        if ($activeOrder) {
            $orderId = $activeOrder['id'];
        } else {
            $createOrder = $pdo->prepare("
                INSERT INTO orders (
                    customer_id, 
                    status, 
                    total_amount, 
                    payment_method, 
                    shipping_address
                ) VALUES (?, 'Pending', 0.00, 'Bank Transfer', 'Staging Collection - Kigali Hub')
            ");
            $createOrder->execute([$customerId]);
            $orderId = $pdo->lastInsertId();
        }

        // Extract current asset valuation info straight from inventory row safely
        $carValQuery = $pdo->prepare("SELECT price FROM vehicles WHERE id = ?");
        $carValQuery->execute([$vehicleId]);
        $targetCar = $carValQuery->fetch();

        if ($targetCar) {
            $priceAtPurchase = $targetCar['price'];

            // Confirm whether this precise slot allocation already exists inside order_details
            $checkDetails = $pdo->prepare("SELECT id FROM order_details WHERE order_id = ? AND vehicle_id = ?");
            $checkDetails->execute([$orderId, $vehicleId]);

            if (!$checkDetails->fetch()) {
                // Execute layout injection using your exact custom columns
                $insertDetail = $pdo->prepare("INSERT INTO order_details (order_id, vehicle_id, quantity, price_at_purchase) VALUES (?, ?, 1, ?)");
                $insertDetail->execute([$orderId, $vehicleId, $priceAtPurchase]);
            }
        }

        $pdo->commit();
        
        // Redirect directly to the dashboard viewport wrapper
        header("Location: garage.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Transaction processing error safely aborted: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetik | <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span>.</span></a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="collection.php">Collection</a>
                <a href="garage.php">My Garage</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact us</a>
            </div>
        </div>
    </nav>

    <main class="container product-layout" style="margin-top: 40px; margin-bottom: 60px;">
        <div class="product-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; align-items: start;">
            
            <div class="product-media-panel">
                <div class="glass-panel main-image-wrapper" style="padding: 20px; background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px;">
                    <img src="<?php echo htmlspecialchars($car['main_image']); ?>" alt="Vehicle Presentation Assets" style="width: 100%; height: auto; border-radius: 4px; border: 1px solid rgba(255,255,255,0.05);">
                </div>
            </div>

            <div class="product-spec-panel glass-panel" style="padding: 35px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px;">
                <span class="category-tag" style="text-transform: uppercase; font-size: 11px; letter-spacing: 1.5px; color: var(--accent-red); font-weight: 700;"><?php echo htmlspecialchars($car['category']); ?></span>
                <h1 style="font-size: 32px; font-weight: 900; color: #fff; margin-top: 10px; margin-bottom: 5px;"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?></h1>
                <p class="price" style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 25px;">$<?php echo number_format($car['price']); ?></p>

                <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 30px;"><?php echo htmlspecialchars($car['description']); ?></p>

                <div class="specs-matrix" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 35px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 25px;">
                    <div>
                        <span style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Output Rating</span>
                        <span style="font-size: 16px; font-weight: 700; color: #fff; font-family: monospace;"><?php echo htmlspecialchars($car['horsepower']); ?> BHP</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Top Velocity</span>
                        <span style="font-size: 16px; font-weight: 700; color: #fff; font-family: monospace;"><?php echo htmlspecialchars($car['top_speed_kmh']); ?> km/h</span>
                    </div>
                </div>

                <form action="product.php?id=<?php echo $car['id']; ?>" method="POST">
                    <input type="hidden" name="action_type" value="stage_asset">
                    
                    <?php if ($isLoggedIn): ?>
                        <button type="submit" class="cta-primary" style="width: 100%; border: none; padding: 14px; background: #fff; color: #000; font-weight: 800; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; border-radius: 4px; cursor: pointer;">Add to My Garage</button>
                    <?php else: ?>
                        <button type="submit" class="cta-primary" style="width: 100%; border: none; padding: 14px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.4); font-weight: 800; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; border-radius: 4px; cursor: pointer;">Login to Save Asset</button>
                    <?php endif; ?>
                </form>
            </div>

        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2026 Kinetik Luxury Motors. Created by Malaz</p>
            <p class="academic-credit">24579/2024</p>
        </div>
    </footer>

</body>
</html>