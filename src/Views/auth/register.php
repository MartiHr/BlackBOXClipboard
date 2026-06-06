<h2>Регистрация</h2>

<?php if (!empty($message)): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Потребителско име" required><br><br>
    <input type="email" name="email" placeholder="Имейл" required><br><br>
    <input type="password" name="password" placeholder="Парола" required><br><br>
    <button type="submit">Регистрирай ме</button>
</form>
