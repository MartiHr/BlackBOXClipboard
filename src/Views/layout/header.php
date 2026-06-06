<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BlackBOX Clipboard') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Начало</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Здравей, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                <a href="/logout">Изход</a>
            <?php else: ?>
                <a href="/login">Вход</a>
                <a href="/register">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
