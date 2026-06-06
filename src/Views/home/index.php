<h1>Добре дошли в BlackBOX Clipboard</h1>
<p>Разпределена система за споделяне на данни в реално време.</p>

<h3>Налични публични канали:</h3>
<ul>
    <?php if (!empty($clipboards)): ?>
        <?php foreach ($clipboards as $c): ?>
            <li>
                <a href="index.php?page=clipboard&slug=<?= htmlspecialchars($c['slug']) ?>">
                    <?= htmlspecialchars($c['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>Все още няма публични канали.</li>
    <?php endif; ?>
</ul>
