<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetik. | contact us</title>
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
            </div>
        </div>
    </nav>

    <main class="container contact-container">
        <section class="contact-header">
            <span class="category-tag">We'd love to Hear from you</span>
            <h1>Contact us</h1>
            <p>Connect with the Kigali Staging Hub. For secure escrow inquiries, fleet logistics, or technical support, please transmit your request below.</p>
        </section>

        <section class="glass-panel contact-form-wrapper">
            <form action="#" method="POST" class="contact-form">
                <div class="form-group">
                    <label>Full name</label>
                    <input type="text" placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label>Contact information</label>
                    <input type="email" placeholder="example@encrypted.com">
                </div>
                <div class="form-group">
                    <label>topic</label>
                    <select>
                        <option>General Fleet Inquiry</option>
                        <option>Escrow Onboarding</option>
                        <option>Logistical Support</option>
                        <option>Technical Diagnostics</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Details</label>
                    <textarea rows="5" placeholder="Detailed message..."></textarea>
                </div>
                <button type="submit" class="cta-primary">Send</button>
            </form>
        </section>
    </main>
    
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2026 Kinetik Luxury Motors. Created by Malaz</p>
            <p class="academic-credit">24579/2024</p>
        </div>
    </footer>
</body>
</html>