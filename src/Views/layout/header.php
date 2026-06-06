<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BlackBOX Clipboard') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/shared.css">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ((array) $extraCss as $css): ?>
            <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($extraJs)): ?>
        <?php foreach ((array) $extraJs as $js): ?>
            <script src="/assets/js/<?= htmlspecialchars($js) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="brand-logo">
                <span>BlackBOX</span>
                <?php if (!empty($showStatus)): ?>
                <div class="status-indicator">
                    <div class="pulse-dot"></div>
                    <span class="status-text"><?= htmlspecialchars($statusText ?? 'Синхр. активно') ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($showNav)): ?>
            <nav class="nav-links">
                <a href="/" <?= ($currentPage === 'home') ? 'class="active"' : '' ?>>Клипборди</a>
                <a href="/clipboard/create">Нов клипборд</a>
            </nav>
            <?php endif; ?>
            <div class="header-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="/logout" class="btn btn-outline">Изход</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline">Вход</a>
                    <a href="/register" class="btn btn-primary">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="<?= htmlspecialchars($mainClass ?? 'dashboard-main') ?>">
