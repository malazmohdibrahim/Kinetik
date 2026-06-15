<?php
session_start();
require_once 'database/connection.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $action = $_POST['action'];

    try {
        if ($action === 'register') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullName, $email, $hashed, $phone]);
            $message = "Identity provisioned. You may now login.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit();
            } else {
                $message = "Authentication failure.";
            }
        }
    } catch (Exception $e) {
        $message = "Registration Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kinetik | Identity Access</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <main class="glass-panel" style="width: 450px; padding: 40px;">
        <h2 style="text-align:center; margin-bottom:20px;">SYSTEM ACCESS</h2> <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">KINETIK<span>.</span></a>
            <div class="nav-links">
                <a href="index.php">home</a>
                <a href="collection.php">Collection</a>
                <a href="garage.php">My Garage</a>
            </div>
        </div>
    </nav>
        <?php if($message) echo "<p style='color:var(--accent-red); text-align:center; font-size:12px;'>$message</p>"; ?>
        
        <form method="POST">
            <div class="form-group" id="reg-fields" style="display:none;">
                  <a href="index.php">return</a>
                <label>Full Name</label>
                <input type="text" name="full_name" style="width:100%; margin-bottom:10px;">
                <label>Phone Contact</label>
                <input type="text" name="phone" style="width:100%; margin-bottom:10px;">
            </div>
            <div class="form-group">
                <label>Email Credential</label>
                <input type="email" name="email" required style="width:100%; margin-bottom:10px;">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required style="width:100%;">
            </div>
            
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" name="action" value="login" class="cta-primary" style="flex:1;">LOGIN</button>
                <button type="button" id="toggle-btn" class="cta-primary" style="flex:1; background:#333 !important;" onclick="toggleReg()">NEW ACCOUNT</button>
                <button type="submit" name="action" value="register" id="reg-submit" class="cta-primary" style="display:none; flex:1;">REGISTER</button>
            </div>
        </form>
    </main>

    <script>
        function toggleReg() {
            document.getElementById('reg-fields').style.display = 'block';
            document.getElementById('reg-submit').style.display = 'block';
            document.getElementById('toggle-btn').style.display = 'none';
        }
    </script>
</body>
</html>