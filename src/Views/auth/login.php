<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | BlackBOX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/shared.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>

    <header class="main-header">
        <div class="header-container">
            <div class="brand-logo">
                <span>BlackBOX</span>
                <div class="status-indicator">
                    <div class="pulse-dot"></div>
                    <span class="status-text">Sync Active</span>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="auth-card">
            <div class="card-header">
                <h1>Welcome Back</h1>
                <p>Access your universal clipboard from any device.</p>
            </div>

            <form id="loginForm" method="POST">
                <?php if (!empty($error)): ?>
                    <div class="form-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="identifier">Username / Email</label>
                    <input type="text" id="identifier" name="username" class="form-control mono-input" placeholder="e.g. dev_user_01" required>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Password</label>
                        <a href="#">Forgot?</a>
                    </div>
                    <input type="password" id="password" name="password" class="form-control mono-input" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <div class="form-footer-redirect">
                <p>Don't have an account? <a href="/register">Sign up</a></p>
            </div>

            <div class="encryption-badge">
                <span class="material-symbols-outlined">lock</span>
                <span>End-to-End Encryption</span>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <span class="footer-copyright">© 2026 BlackBOX Clipboard. All rights reserved.</span>
        </div>
    </footer>

</body>
</html>
