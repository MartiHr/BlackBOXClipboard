    </main>
    <footer class="main-footer">
        <div class="footer-container">
            <span class="footer-copyright">&copy; <?= date('Y') ?> BlackBOX Clipboard. Всички права запазени.</span>
        </div>
    </footer>
    <?php if (!empty($extraScripts)): ?>
    <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
