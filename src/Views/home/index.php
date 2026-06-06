        <section class="dashboard-header">
            <div>
                <h1>Клипборди</h1>
                <p class="dashboard-title-desc">
                    Клипборди за синхронизация в реално време за вашите клипборд фрагменти. Наблюдавайте състоянието и прехвърляйте данни между инстанции.
                </p>
            </div>
            <a href="/clipboard/create" class="btn btn-primary btn-fit">
                <span class="material-symbols-outlined">add</span> Нов клипборд
            </a>
        </section>

        <section class="bento-grid">
            <?php if (!empty($clipboards)): ?>
                <?php foreach ($clipboards as $c): ?>
                    <div class="channel-card">
                        <div class="card-top">
                            <div>
                                <span class="channel-status <?= $c['visibility'] === 'private' ? 'channel-status-standby' : '' ?>">
                                    <?= $c['visibility'] === 'private' ? 'Частен' : 'Публичен' ?> клипборд
                                </span>
                                <h3><?= htmlspecialchars($c['name']) ?></h3>
                            </div>
                            <span class="material-symbols-outlined">
                                <?= $c['visibility'] === 'private' ? 'lock' : 'terminal' ?>
                            </span>
                        </div>
                        <div class="card-stats">
                            <div class="stat-row">
                                <span>Създаден:</span>
                                <span><?= htmlspecialchars(date('M j, Y', strtotime($c['created_at']))) ?></span>
                            </div>
                            <div class="stat-row">
                                <span>Вид:</span>
                                <span><?= $c['mode'] === 'multi' ? 'Множествен' : 'Единичен' ?></span>
                            </div>
                            <a href="/clipboard?id=<?= htmlspecialchars($c['id']) ?>" class="btn btn-outline card-action-btn">Отвори</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                
                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status">Активен хъб</span>
                            <h3>Production_Main</h3>
                        </div>
                        <span class="material-symbols-outlined">terminal</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Последна актуализация:</span><span>преди 2 мин.</span></div>
                        <div class="stat-row"><span>Пакети:</span><span>1,248</span></div>
                        <button class="btn btn-outline card-action-btn">Отвори</button>
                    </div>
                </div>

            <?php endif; ?>

            <a href="/clipboard/create" class="empty-card">
                <div class="icon-circle">
                    <span class="material-symbols-outlined">add</span>
                </div>
                <span class="empty-card-text">Нов клипборд</span>
            </a>
        </section>

        <?php if (!empty($pagination) && $pagination['total'] > 1): ?>
        <section class="pagination-row">
            <?php
                $prevPage = $pagination['current'] - 1;
                $nextPage = $pagination['current'] + 1;
                $prevDisabled = $pagination['current'] <= 1;
                $nextDisabled = $pagination['current'] >= $pagination['total'];
                $prevHref = $prevDisabled ? '#' : '/?page=' . $prevPage;
                $nextHref = $nextDisabled ? '#' : '/?page=' . $nextPage;
            ?>
            <a class="pagination-button<?= $prevDisabled ? ' disabled' : '' ?>" href="<?= htmlspecialchars($prevHref) ?>" aria-disabled="<?= $prevDisabled ? 'true' : 'false' ?>">
                <span class="material-symbols-outlined">chevron_left</span>
            </a>

            <span class="pagination-page-indicator">
                Страница <?= $pagination['current'] ?> от <?= $pagination['total'] ?>
            </span>

            <a class="pagination-button<?= $nextDisabled ? ' disabled' : '' ?>" href="<?= htmlspecialchars($nextHref) ?>" aria-disabled="<?= $nextDisabled ? 'true' : 'false' ?>">
                <span class="material-symbols-outlined">chevron_right</span>
            </a>
        </section>
        <?php endif; ?>

        <section class="stats-section">
            <div class="stats-card">
                <div class="progress-bar-container">
                    <span class="progress-bar-label">Глобален трафик</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= htmlspecialchars($traffic['percent']) ?>%"></div>
                    </div>
                </div>
                <div class="stats-value-box">
                    <span class="stats-large-num"><?= htmlspecialchars($traffic['display']) ?></span>
                    <span class="status-text"><?= htmlspecialchars($traffic['count']) ?> елемента днес</span>
                </div>
            </div>

            <div class="stats-card stats-card-vertical">
                <span class="progress-bar-label">Свързани възли</span>
                <span class="stats-large-num"><?= count($clipboards) ?></span>
            </div>
        </section>
