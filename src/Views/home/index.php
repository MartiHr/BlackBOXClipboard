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

        <form class="filter-bar" method="GET" action="/">
            <input type="text" name="search" class="form-control filter-search" placeholder="Търси клипборд..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            <select name="visibility" class="form-control filter-select">
                <option value="">Всички видимости</option>
                <option value="public" <?= ($filters['visibility'] ?? '') === 'public' ? 'selected' : '' ?>>Публични</option>
                <option value="private" <?= ($filters['visibility'] ?? '') === 'private' ? 'selected' : '' ?>>Частни</option>
            </select>
            <select name="mode" class="form-control filter-select">
                <option value="">Всички режими</option>
                <option value="single" <?= ($filters['mode'] ?? '') === 'single' ? 'selected' : '' ?>>Единичен</option>
                <option value="multi" <?= ($filters['mode'] ?? '') === 'multi' ? 'selected' : '' ?>>Множествен</option>
            </select>
            <select name="sort" class="form-control filter-select">
                <option value="created" <?= ($filters['sort'] ?? 'created') === 'created' ? 'selected' : '' ?>>По дата</option>
                <option value="name" <?= ($filters['sort'] ?? '') === 'name' ? 'selected' : '' ?>>По име</option>
            </select>
            <select name="order" class="form-control filter-select" style="min-width:80px">
                <option value="desc" <?= ($filters['order'] ?? 'desc') === 'desc' ? 'selected' : '' ?>>Низх.</option>
                <option value="asc" <?= ($filters['order'] ?? '') === 'asc' ? 'selected' : '' ?>>Възх.</option>
            </select>
            <button type="submit" class="btn btn-primary btn-fit">Филтър</button>
            <a href="/" class="btn btn-outline btn-fit">Изчисти</a>
        </form>

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
                $queryParams = array_intersect_key($filters, array_flip(['search', 'visibility', 'mode', 'sort', 'order']));
                $queryString = http_build_query($queryParams);
                $prevPage = $pagination['current'] - 1;
                $nextPage = $pagination['current'] + 1;
                $prevDisabled = $pagination['current'] <= 1;
                $nextDisabled = $pagination['current'] >= $pagination['total'];
                $prevHref = $prevDisabled ? '#' : '/?page=' . $prevPage . ($queryString ? '&' . $queryString : '');
                $nextHref = $nextDisabled ? '#' : '/?page=' . $nextPage . ($queryString ? '&' . $queryString : '');
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
