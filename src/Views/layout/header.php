<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BlackBOX Clipboard') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/shared.css">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ((array) $extraCss as $css): ?>
            <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="<?= BASE_PATH ?>/assets/js/main.js" defer></script>
    <?php if (!empty($extraJs)): ?>
        <?php foreach ((array) $extraJs as $js): ?>
            <script src="<?= BASE_PATH ?>/assets/js/<?= htmlspecialchars($js) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script>window.BASE_PATH = '<?= BASE_PATH ?>';</script>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="brand-logo">
                <a href="<?= BASE_PATH ?>/" class="brand-link">BlackBOX</a>
                <?php if (!empty($showStatus)): ?>
                <div class="status-indicator">
                    <div class="pulse-dot"></div>
                    <span class="status-text"><?= htmlspecialchars($statusText ?? 'Синхр. активно') ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($showNav)): ?>
            <nav class="nav-links">
                <a href="<?= BASE_PATH ?>/" <?= ($currentPage === 'home') ? 'class="active"' : '' ?>>Клипборди</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_PATH ?>/clipboard/create">Нов клипборд</a>
                    <?php if (($_SESSION['role'] ?? null) === 'admin'): ?>
                        <a href="<?= BASE_PATH ?>/admin" <?= ($currentPage === 'admin') ? 'class="active"' : '' ?>>Админ</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= BASE_PATH ?>/clipboard/share">Следвай клипборд</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
            <div class="header-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="notif-wrapper">
                        <button id="notifBell" class="notif-bell" title="Нотификации">
                            <span class="material-symbols-outlined">notifications</span>
                            <span id="notifBadge" class="notif-badge" style="display:none">0</span>
                        </button>
                        <div id="notifDropdown" class="notif-dropdown" style="display:none">
                            <div class="notif-dropdown-header">
                                <span>Нотификации</span>
                                <button id="markAllReadBtn" class="btn btn-text">Маркирай всички</button>
                            </div>
                            <div id="notifList" class="notif-list"></div>
                        </div>
                    </div>
                    <a href="<?= BASE_PATH ?>/profile" class="header-username"><?= htmlspecialchars($_SESSION['username']) ?></a>
                    <a href="<?= BASE_PATH ?>/logout" class="btn btn-outline">Изход</a>
                <?php else: ?>
                    <a href="<?= BASE_PATH ?>/login" class="btn btn-outline">Вход</a>
                    <a href="<?= BASE_PATH ?>/register" class="btn btn-primary">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="<?= htmlspecialchars($mainClass ?? 'dashboard-main') ?>">
