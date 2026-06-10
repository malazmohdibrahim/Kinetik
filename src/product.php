<?php
require_once 'database/connection.php';

$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

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
                <a href="index.php">Showroom</a>
                <a href="collection.php">Collection</a>
                <a href="garage.php" class="active">My Garage</a>
            </div>
        </div>
    </nav>

    <main class="container stacked-product-container">
        
        <div id="popupOverlay" class="popup-overlay">
            <div class="popup-card glass-panel">
                <h3>⚡ ACTION INITIALIZED</h3>
                <p>Test drive arrangement logged. Our concierge team at the Kigali staging hub will finalize tracking credentials shortly.</p>
                <button id="closePopup" class="cta-primary-sm">Acknowledge</button>
            </div>
        </div>

        <section class="visualizer-panel-stacked glass-panel">
            <div class="viewer-container-stacked">
                <?php foreach ($frames as $index => $frame): ?>
                    <img src="<?php echo htmlspecialchars($frame['image_path']); ?>" class="spin-frame <?php echo $index === 0 ? 'active-frame' : ''; ?>" data-index="<?php echo $index; ?>">
                <?php endforeach; ?>
            </div>
            <div class="visualizer-controls-stacked">
                <span class="control-label">DRAG TO ROTATE 360° EXPERIENCES</span>
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
                    <span class="price-label">Acquisition Value</span>
                    <p class="product-price">$<?php echo number_format($car['price']); ?></p>
                </div>
            </div>
            <p class="product-description-stacked"><?php echo htmlspecialchars($car['description']); ?></p>
            <div class="performance-matrix-stacked">
                <div class="matrix-item"><span class="matrix-value"><?php echo htmlspecialchars($car['horsepower']); ?> BHP</span><span class="matrix-label">Output Power</span></div>
                <div class="matrix-item"><span class="matrix-value"><?php echo htmlspecialchars($car['top_speed_kmh']); ?> km/h</span><span class="matrix-label">V-Max Velocity</span></div>
                <div class="matrix-item"><span class="matrix-value">Kigali Hub</span><span class="matrix-label">Staging Base</span></div>
            </div>
        </section>

        <section class="payment-panel-stacked glass-panel">
            <div class="secure-badge-row">
                <div class="secure-title">
                    <h3>FLEET MANAGEMENT INTERFACE</h3>
                    <p>Select an operation protocol below to allocate this vehicle token to your portfolio.</p>
                </div>
            </div>

            <div class="dual-action-grid">
                <button type="button" id="bookDriveBtn" class="cta-secondary">Book Local Test Drive</button>

                <form action="garage_process.php" method="POST">
                    <input type="hidden" name="vehicle_id" value="<?php echo $car['id']; ?>">
                    <button type="submit" class="cta-primary">Add to My Garage</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 360 Frame Rotation Logic
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

            // Minimalist Front-End Popup Toggles
            const bookDriveBtn = document.getElementById('bookDriveBtn');
            const popupOverlay = document.getElementById('popupOverlay');
            const closePopup = document.getElementById('closePopup');

            if (bookDriveBtn && popupOverlay && closePopup) {
                bookDriveBtn.addEventListener('click', () => {
                    popupOverlay.classList.add('visible-popup');
                });

                closePopup.addEventListener('click', () => {
                    popupOverlay.classList.remove('visible-popup');
                });
            }
        });
    </script>
</body>
</html>