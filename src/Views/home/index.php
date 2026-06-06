        <section class="dashboard-header">
            <div>
                <div class="status-indicator">
                    <div class="pulse-dot"></div>
                    <span class="status-text">Всички системи работят</span>
                </div>
                <h1>Клипборди</h1>
                <p class="dashboard-title-desc">
                    Клинични хъбове за синхронизация в реално време за вашите клипборд фрагменти. Наблюдавайте състоянието и прехвърляйте данни между инстанции.
                </p>
            </div>
            <button class="btn btn-primary btn-fit">
                <span class="material-symbols-outlined">add</span> Нов клипборд
            </button>
        </section>

        <section class="bento-grid">
            <?php if (!empty($clipboards)): ?>
                <?php foreach ($clipboards as $c): ?>
                    <div class="channel-card">
                        <div class="card-top">
                            <div>
                                <span class="channel-status <?= $c['visibility'] === 'private' ? 'channel-status-standby' : '' ?>">
                                    <?= $c['visibility'] === 'private' ? 'Частен' : 'Публичен' ?> хъб
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
                                <span>Идентификатор:</span>
                                <span><?= htmlspecialchars($c['slug']) ?></span>
                            </div>
                            <a href="/clipboard/<?= htmlspecialchars($c['slug']) ?>" class="btn btn-outline card-action-btn">Отвори</a>
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

                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status channel-status-standby">В режим на готовност</span>
                            <h3>Staging_Vault</h3>
                        </div>
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Последна актуализация:</span><span>преди 1 час</span></div>
                        <div class="stat-row"><span>Пакети:</span><span>42</span></div>
                        <button class="btn btn-outline card-action-btn">Отвори</button>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status">Авто-синхр.</span>
                            <h3>Mobile_Proxy</h3>
                        </div>
                        <span class="material-symbols-outlined">smartphone</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Последна актуализация:</span><span>Току-що</span></div>
                        <div class="stat-row"><span>Пакети:</span><span>8,912</span></div>
                        <button class="btn btn-outline card-action-btn">Отвори</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="empty-card">
                <div class="icon-circle">
                    <span class="material-symbols-outlined">add</span>
                </div>
                <span class="empty-card-text">Нов клипборд</span>
            </div>
        </section>

        <section class="stats-section">
            <div class="stats-card">
                <div class="progress-bar-container">
                    <span class="progress-bar-label">Глобален трафик</span>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                <div class="stats-value-box">
                    <span class="stats-large-num">8.4 GB</span>
                    <span class="status-text">Прехвърлено днес</span>
                </div>
            </div>

            <div class="stats-card stats-card-vertical">
                <span class="progress-bar-label">Свързани възли</span>
                <span class="stats-large-num"><?= count($clipboards) ?></span>
            </div>
        </section>
