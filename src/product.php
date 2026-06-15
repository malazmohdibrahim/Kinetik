<?php
session_start();
require_once 'database/connection.php';

// 1. Context initialization parameters
$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$isLoggedIn = isset($_SESSION['user_id']);
$customerId = $isLoggedIn ? $_SESSION['user_id'] : null;

// 2. DATABASE MECHANICS: Intercept submission and write directly into order_details schema
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'stage_asset') {
    // Hard check: Block processing if an unauthenticated user somehow targets the endpoint
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit();
    }

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

// 3. READ OPERATIONAL PROTOCOLS: Gather vehicle specifications and 360° frame assets
try {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$vehicleId]);
    $car = $stmt->fetch();

    if (!$car) {
        die("Vehicle not found in showroom inventory.");
    }

    $imgStmt = $pdo->prepare("SELECT image_path, caption FROM vehicle_images WHERE vehicle_id = ? ORDER BY caption ASC");
    $imgStmt->execute([$vehicleId]);
    $frames = $imgStmt->fetchAll();

    if (empty($frames)) {
        $frames[] = ['image_path' => $car['main_image'], 'caption' => 'Default Main View'];
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
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
                <a href="index.php">home</a>
                <a href="collection.php">Collection</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact us</a>
                <a href="garage.php" class="active">My Garage</a>
            </div>
        </div>
    </nav>

    <main class="container stacked-product-container">
        
        <div id="popupOverlay" style="display: none; opacity: 0; transition: opacity 0.3s ease; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); backdrop-filter: blur(8px); align-items: center; justify-content: center; z-index: 9999;">
            <div class="popup-card" style="background: #0d0d0d; border: 1px solid rgba(255,255,255,0.1); padding: 40px; text-align: center; border-radius: 8px; max-width: 400px; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                <h3 style="letter-spacing: 2px; color: #fff; margin-bottom: 12px; font-weight: 900; font-family: sans-serif;">CONFIRMED</h3>
                <p style="color: #888; font-size: 13px; line-height: 1.6; margin-bottom: 24px; font-family: sans-serif;">We will contact you shortly.</p>
                <button id="closePopup" style="background: #fff; color: #000; border: none; padding: 12px 28px; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; border-radius: 4px; cursor: pointer;">Acknowledge</button>
            </div>
        </div>

        <section class="visualizer-panel-stacked glass-panel">
            <div class="viewer-container-stacked">
                <?php foreach ($frames as $index => $frame): ?>
                    <img src="<?php echo htmlspecialchars($frame['image_path']); ?>" class="spin-frame <?php echo $index === 0 ? 'active-frame' : ''; ?>" data-index="<?php echo $index; ?>">
                <?php endforeach; ?>
            </div>
            <div class="visualizer-controls-stacked">
                <span class="control-label">DRAG TO ROTATE 360° </span>
                <input type="range" min="0" max="<?php echo count($frames) - 1; ?>" value="0" class="rotation-slider" id="spinSlider">
                <div class="frame-indicator">Showroom Frame: <span id="frameNum">1</span> / <?php echo count($frames); ?></div>
            </div>
        </section>

        <section class="details-panel-stacked glass-panel">
            <div class="details-header-split">
                <div>
                    <span class="category-tag"><?php echo htmlspecialchars($car['category']); ?></span>
                    <h1><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model_name']); ?></h1>
                </div>
                <div class="price-block">
                    <span class="price-label">price</span>
                    <p class="product-price">$<?php echo number_format($car['price']); ?></p>
                </div>
            </div>
            <p class="product-description-stacked"><?php echo htmlspecialchars($car['description'] ?? ''); ?></p>
            <div class="performance-matrix-stacked">
                <div class="matrix-item"><span class="matrix-value"><?php echo htmlspecialchars($car['horsepower'] ?? 'N/A'); ?> BHP</span><span class="matrix-label">Output Power</span></div>
                <div class="matrix-item"><span class="matrix-value"><?php echo htmlspecialchars($car['top_speed_kmh'] ?? 'N/A'); ?> km/h</span><span class="matrix-label">V-Max Velocity</span></div>
                <div class="matrix-item"><span class="matrix-value">Kigali Hub</span><span class="matrix-label">location</span></div>
            </div>
        </section>

        <section class="payment-panel-stacked glass-panel">
            <div class="secure-badge-row">
                <div class="secure-title">
                    <p>Do you like this car?</p>
                </div>
            </div>

            <div class="dual-action-grid">
                <?php if ($isLoggedIn): ?>
                    <button type="button" id="bookDriveBtn" class="cta-secondary">Book Test Drive</button>

                    <form action="" method="POST" style="margin: 0;">
                        <input type="hidden" name="action_type" value="stage_asset">
                        <button type="submit" class="cta-primary">Add to My Garage</button>
                    </form>
                <?php else: ?>
                    <div style="grid-column: span 2; text-align: center; padding: 10px 0;">
                        <p style="font-size: 13px; color: #888; margin-bottom: 15px;">Please log in to book a test drive or save cars to your garage.</p>
                        <a href="login.php" class="cta-primary" style="display: inline-block; text-decoration: none; background: #1e1b4b !important; padding: 12px 40px;">Login first</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Slider rotation UI handlers
            const slider = document.getElementById('spinSlider');
            const frames = document.querySelectorAll('.spin-frame');
            const frameNumIndicator = document.getElementById('frameNum');
            
            if (slider && frames.length > 0) {
                slider.addEventListener('input', (e) => {
                    const targetIndex = parseInt(e.target.value, 10);
                    frameNumIndicator.textContent = targetIndex + 1;
                    frames.forEach(img => {
                        if (parseInt(img.getAttribute('data-index'), 10) === targetIndex) {
                            img.classList.add('active-frame');
                        } else {
                            img.classList.remove('active-frame');
                        }
                    });
                });
            }

            // Visual notification toggle controller
            const bookDriveBtn = document.getElementById('bookDriveBtn');
            const popupOverlay = document.getElementById('popupOverlay');
            const closePopup = document.getElementById('closePopup');

            if (bookDriveBtn && popupOverlay && closePopup) {
                bookDriveBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    popupOverlay.style.display = 'flex';
                    setTimeout(() => { popupOverlay.style.opacity = '1'; }, 10);
                });

                closePopup.addEventListener('click', () => {
                    popupOverlay.style.opacity = '0';
                    setTimeout(() => { popupOverlay.style.display = 'none'; }, 300);
                });
            }
        });
    </script>
</body>
</html>