<div class="profile-panel">
    <div class="profile-header">
        <div class="profile-avatar">
            <span class="material-symbols-outlined">account_circle</span>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
            <span class="profile-role"><?= $user['role'] === 'admin' ? 'Администратор' : 'Потребител' ?></span>
        </div>
    </div>

    <section class="profile-section">
        <h2>Абонаменти</h2>
        <?php if (empty($subscriptions)): ?>
            <p class="profile-empty">Нямате активни абонаменти.</p>
        <?php else: ?>
            <div class="sub-list">
                <?php foreach ($subscriptions as $sub): ?>
                    <div class="sub-item">
                        <a href="<?= BASE_PATH ?>/clipboard?id=<?= (int) $sub['clipboard_id'] ?>" class="sub-link">
                            <span class="sub-name"><?= htmlspecialchars($sub['clipboard_name']) ?></span>
                        </a>
                        <button class="sub-toggle" data-clipboard-id="<?= (int) $sub['clipboard_id'] ?>" data-enabled="<?= $sub['notify_email'] ? '1' : '0' ?>" title="Email нотификации">
                            <span class="material-symbols-outlined sub-icon"><?= $sub['notify_email'] ? 'mail' : 'notifications' ?></span>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<script src="<?= BASE_PATH ?>/assets/js/profile.js" defer></script>
