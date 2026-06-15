<?php
session_start();
require_once 'database/connection.php';

// 1. Strict Security Gatekeeping: Reject anything other than a confirmed administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$statusMessage = "";

// 2. BACKEND ACTION INTERCEPTORS (Add / Delete Operational Mechanics)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ACTION A: Deploy/Add New Vehicle Asset
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'add_vehicle') {
        $brand = trim($_POST['brand'] ?? '');
        $modelName = trim($_POST['model_name'] ?? '');
        $category = trim($_POST['category'] ?? 'Sports');
        $price = (float)($_POST['price'] ?? 0);
        $horsepower = (int)($_POST['horsepower'] ?? 0);
        $topSpeed = (int)($_POST['top_speed'] ?? 0);
        $mainImage = trim($_POST['main_image'] ?? 'assets/images/default.jpg');
        $description = trim($_POST['description'] ?? '');

        if (!empty($brand) && !empty($modelName) && $price > 0) {
            try {
                $insertStmt = $pdo->prepare("
                    INSERT INTO vehicles (brand, model_name, category, price, horsepower, top_speed_kmh, main_image, description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$brand, $modelName, $category, $price, $horsepower, $topSpeed, $mainImage, $description]);
                $statusMessage = "Asset matrix initialized successfully.";
            } catch (Exception $e) {
                $statusMessage = "Error deploying asset: " . $e->getMessage();
            }
        } else {
            $statusMessage = "Validation failed. Brand, Model, and Price parameters are mandatory.";
        }
    }

    // ACTION B: Erase / Delete Vehicle Asset from Showroom Staging
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'delete_vehicle') {
        $targetId = (int)($_POST['target_id'] ?? 0);
        if ($targetId > 0) {
            try {
                // First, drop or clear matching line records inside order_details to protect schema cascade integrity
                $clearDetails = $pdo->prepare("DELETE FROM order_details WHERE vehicle_id = ?");
                $clearDetails->execute([$targetId]);

                // Clear optional associated frame logs if existing
                $clearImages = $pdo->prepare("DELETE FROM vehicle_images WHERE vehicle_id = ?");
                $clearImages->execute([$targetId]);

                // Purge primary entity mapping row from inventory
                $deleteStmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
                $deleteStmt->execute([$targetId]);
                
                $statusMessage = "Asset mapping token #" . $targetId . " purged permanently.";
            } catch (Exception $e) {
                $statusMessage = "Purge execution failed safely: " . $e->getMessage();
            }
        }
    }
}

// 3. READ METRICS & LOG pipelines
try {
    // Analytics: Fetch operational counters in real time
    $countVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
    $countOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // Pull entire vehicle fleet stock
    $vehicleQuery = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC");
    $vehicles = $vehicleQuery->fetchAll();

    // Pull pending and completed staging tracking pipelines
    $orderQuery = $pdo->query("
        SELECT o.id, o.status, o.created_at, u.full_name as customer_name, v.brand, v.model_name 
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        JOIN order_details od ON o.id = od.order_id
        JOIN vehicles v ON od.vehicle_id = v.id
        ORDER BY o.created_at DESC
    ");
    $orders = $orderQuery->fetchAll();
} catch (Exception $e) {
    die("Showroom Diagnostics Query Fault: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetik Operations | Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="admin.php" class="logo">KINETIK<span>.</span>OPERATIONS</a>
            <div class="nav-links">
                <a href="admin.php" class="active">Dashboard</a>
                <a href="index.php">View Showroom</a>
                <a href="logout.php" style="color: #f43f5e;">Secure Exit</a>
            </div>
        </div>
    </nav>

    <main class="container" style="margin-top: 40px; margin-bottom: 60px;">
        
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span style="font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #a78bfa;">Control Center Panel</span>
                <h1 style="font-size: 28px; margin-top: 5px;">Showroom Management Strategy</h1>
            </div>
            
            <?php if(!empty($statusMessage)): ?>
                <div class="glass-panel" style="padding: 10px 20px; border-left: 3px solid #cbd5e1; font-size: 13px; color: #cbd5e1;">
                    ⚡ <?php echo htmlspecialchars($statusMessage); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="analytics-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 40px;">
            <div class="glass-panel" style="padding: 25px; display: flex; flex-direction: column; gap: 5px;">
                <span style="font-size: 12px; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase;">Total Fleet Inventory</span>
                <span style="font-size: 36px; font-weight: bold; color: #fff;"><?php echo $countVehicles; ?> <small style="font-size: 14px; font-weight: normal; color: #64748b;">Active Units</small></span>
            </div>
            <div class="glass-panel" style="padding: 25px; display: flex; flex-direction: column; gap: 5px;">
                <span style="font-size: 12px; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase;">Recent Active Bookings</span>
                <span style="font-size: 36px; font-weight: bold; color: #fff;"><?php echo $countOrders; ?> <small style="font-size: 14px; font-weight: normal; color: #64748b;">Allocations</small></span>
            </div>
        </div>

        <section class="glass-panel" style="padding: 30px; margin-bottom: 40px;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid #1e293b; padding-bottom: 10px; letter-spacing: 1px;">INITIALIZE & DEPLOY NEW VEHICLE</h3>
            
            <form action="" method="POST" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <input type="hidden" name="action_type" value="add_vehicle">
                
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Marque Brand Name</label>
                    <input type="text" name="brand" placeholder="e.g. Porsche" required style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Model Designation</label>
                    <input type="text" name="model_name" placeholder="e.g. 911 Turbo S" required style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Classification Category</label>
                    <select name="category" style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff; height: 38px;">
                        <option value="Hypercar">Hypercar</option>
                        <option value="Supercar">Supercar</option>
                        <option value="Track Edition">Track Edition</option>
                        <option value="Sports">Sports</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Valuation Price ($)</label>
                    <input type="number" name="price" placeholder="e.g. 216000" required style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Output Performance (BHP)</label>
                    <input type="number" name="horsepower" placeholder="e.g. 640" style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">V-Max Top Speed (km/h)</label>
                    <input type="number" name="top_speed" placeholder="e.g. 330" style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div style="grid-column: span 3;">
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Primary Showroom Image Route (File Path)</label>
                    <input type="text" name="main_image" value="assets/images/" placeholder="assets/images/cars/custom-model.jpg" style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff;">
                </div>
                <div style="grid-column: span 3;">
                    <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Marque Profile Engineering Context Description</label>
                    <textarea name="description" placeholder="Specify dynamic performance configuration matrices..." rows="3" style="width: 100%; padding: 10px; background: #000; border: 1px solid #334155; border-radius: 4px; color: #fff; resize: none; font-family: sans-serif;"></textarea>
                </div>
                <div style="grid-column: span 3; text-align: right; margin-top: 10px;">
                    <button type="submit" class="cta-primary" style="padding: 12px 35px; font-weight: bold; font-size: 13px;">Deploy Asset to Catalog</button>
                </div>
            </form>
        </section>

        <section class="glass-panel" style="padding: 30px; margin-bottom: 40px;">
            <h3 style="letter-spacing: 1px; margin-bottom: 20px; border-bottom: 1px solid #1e293b; padding-bottom: 10px;">FLEET INVENTORY TARGET MAPS</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-family: sans-serif; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #1e293b; color: #94a3b8;">
                            <th style="padding: 12px;">Asset ID</th>
                            <th style="padding: 12px;">Marque Profile</th>
                            <th style="padding: 12px;">Category</th>
                            <th style="padding: 12px;">Valuation</th>
                            <th style="padding: 12px;">Performance Metrics</th>
                            <th style="padding: 12px; text-align: center;">Operational Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr style="border-bottom: 1px solid #1e293b; color: #cbd5e1;">
                                <td style="padding: 12px; font-weight: bold;">#INV-<?php echo $vehicle['id']; ?></td>
                                <td style="padding: 12px; color: #fff; font-weight: 500;">
                                    <?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model_name']); ?>
                                </td>
                                <td style="padding: 12px; text-transform: uppercase; font-size: 12px; color: #a78bfa;"><?php echo htmlspecialchars($vehicle['category']); ?></td>
                                <td style="padding: 12px; font-weight: bold; color: #34d399;">$<?php echo number_format($vehicle['price']); ?></td>
                                <td style="padding: 12px; font-size: 13px; color: #94a3b8;">
                                    <?php echo htmlspecialchars($vehicle['horsepower'] ?? 'N/A'); ?> BHP / <?php echo htmlspecialchars($vehicle['top_speed_kmh'] ?? 'N/A'); ?> km/h
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Confirm permanent asset erasure tracking map removal sequence?');">
                                        <input type="hidden" name="action_type" value="delete_vehicle">
                                        <input type="hidden" name="target_id" value="<?php echo $vehicle['id']; ?>">
                                        <button type="submit" style="background: none; border: 1px solid #f43f5e; color: #f43f5e; padding: 5px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f43f5e'; this.style.color='#000';" onmouseout="this.style.background='none'; this.style.color='#f43f5e';">
                                            Purge Asset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="glass-panel" style="padding: 30px;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid #1e293b; padding-bottom: 10px; letter-spacing: 1px;">ACTIVE STAGING & OPERATION LOGS</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-family: sans-serif; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #1e293b; color: #94a3b8;">
                            <th style="padding: 12px;">Order Ref</th>
                            <th style="padding: 12px;">Operator / Client</th>
                            <th style="padding: 12px;">Target Asset Allocation</th>
                            <th style="padding: 12px;">Timestamp</th>
                            <th style="padding: 12px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" style="padding: 20px; text-align: center; color: #64748b;">No staging assets or bookings tracked inside system pipelines yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr style="border-bottom: 1px solid #1e293b; color: #cbd5e1;">
                                    <td style="padding: 12px; font-weight: bold; color: #fff;">#00<?php echo $order['id']; ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($order['brand'] . ' ' . $order['model_name']); ?></td>
                                    <td style="padding: 12px; font-size: 12px; color: #94a3b8;"><?php echo $order['created_at']; ?></td>
                                    <td style="padding: 12px;">
                                        <span style="background: #1e1b4b; color: #a78bfa; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2026 Kinetik Luxury Motors. Admin Infrastructure Environment.</p>
        </div>
    </footer>

</body>
</html>