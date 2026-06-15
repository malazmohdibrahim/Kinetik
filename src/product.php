<?php
session_start();
require_once 'database/connection.php';

// 1. Context initialization parameters
$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); // Enforce strict true/false baseline

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

// 3. READ OPERATIONAL PROTOCOLS: Gather vehicle specifications and 360° frame assets
// ... rest of your code remains identical