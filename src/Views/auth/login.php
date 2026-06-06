<h2>Вход в системата</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Потребителско име" required><br><br>
    <input type="password" name="password" placeholder="Парола" required><br><br>
    <button type="submit">Влез</button>
</form>

<p>Нямате профил? <a href="/register">Регистрирайте се тук</a></p>
