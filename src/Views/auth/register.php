<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | BlackBOX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/shared.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>

    <header class="main-header">
        <div class="header-container">
            <div class="brand-logo">BlackBOX</div>
            <nav class="nav-links">
                <a href="/">Channels</a>
                <a href="#">Create New</a>
            </nav>
            <div class="header-actions">
                <button class="btn btn-outline btn-icon-only"><span class="material-symbols-outlined">sync</span></button>
                <button class="btn btn-outline">Profile</button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="auth-card auth-card-wide">
            <div class="card-header">
                <h1>Create Your Account</h1>
                <p>Join BlackBOX for a seamless universal clipboard experience.</p>
            </div>

            <form method="POST">
                <?php if (!empty($message)): ?>
                    <div class="form-message"><?= $message ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Full Name</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@company.com" required>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>

            <div class="form-footer-redirect">
                <p>Already have an account? <a href="/login">Sign in</a></p>
            </div>

            <div class="encryption-badge">
                <span class="material-symbols-outlined">lock</span>
                <span>End-to-End Encryption</span>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <span class="footer-copyright">© 2024 BlackBOX Clipboard. All rights reserved.</span>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">API Documentation</a>
            </div>
        </div>
    </footer>

</body>
</html>
