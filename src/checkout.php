<?php
session_start();
require_once 'database/connection.php';

$customerId = 1;
$success = false;

// Fetch pending order
$stmt = $pdo->prepare("SELECT id FROM orders WHERE customer_id = ? AND status = 'Pending' LIMIT 1");
$stmt->execute([$customerId]);
$order = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $order) {
    $orderId = $order['id'];
    $paymentMethod = $_POST['payment_method'];
    $shippingAddress = $_POST['shipping_address'] . " | Phone: " . $_POST['phone_number'];

    try {
        $pdo->beginTransaction();
        $calc = $pdo->prepare("SELECT SUM(price_at_purchase * quantity) as total FROM order_details WHERE order_id = ?");
        $calc->execute([$orderId]);
        $total = $calc->fetch()['total'] ?? 0;

        $update = $pdo->prepare("UPDATE orders SET total_amount = ?, payment_method = ?, shipping_address = ?, status = 'Approved' WHERE id = ?");
        $update->execute([$total, $paymentMethod, $shippingAddress, $orderId]);
        
        $pdo->commit();
        $success = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kinetik | Finalize Allocation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container" style="max-width: 600px; margin: 80px auto;">
        <?php if ($success): ?>
            <div class="glass-panel" style="text-align: center; padding: 60px;">
                <h1 style="color: #ff3e3e;">Order Confirmed</h1>
                <p style="color: var(--text-muted);">Your order has been confirmed. we've emailed you the delivery details.</p>
                <a href="index.php" class="cta-primary" style="display:inline-block; margin-top:20px;">Return</a>
            </div>
        <?php elseif ($order): ?>
            <a href="garage.php" style="color: var(--text-muted); font-size: 12px;">← BACK TO GARAGE</a>
            <h1 style="margin: 20px 0;">CHECKOUT</h1>
            <form method="POST" class="checkout-box">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="shipping_address" required rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Direct Contact</label>
                    <input type="text" name="phone_number" required placeholder="+250">
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method">
                        <option>Bank Transfer</option><option>Credit Card</option>
                        <option>Mobile Money</option><option>Crypto</option>
                    </select>
                </div>
                <button type="submit" class="cta-primary" style="width: 100%; margin-top: 20px;">Confirm </button>
            </form>
        <?php else: ?>
            <p>No pending orders found. <a href="index.php">Browse inventory</a>.</p>
        <?php endif; ?>
    </main>
</body>
</html>