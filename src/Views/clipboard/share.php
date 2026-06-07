<?php
$code = $code ?? '';
$error = $error ?? null;
$clipboard = $clipboard ?? null;
$items = $items ?? [];
$lastId = 0;
foreach ($items as $item) {
    if ($item['id'] > $lastId) {
        $lastId = $item['id'];
    }
}
?>

<div class="share-page">
    <div class="breadcrumb">
        <a href="/">Клипборди</a>
        <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
        <span class="breadcrumb-current">Live Share</span>
    </div>

    <section class="share-hero">
        <div class="share-hero-card">
            <div>
                <span class="eyebrow">Достъп по код</span>
                <h1>Следи клипборд с Share Code</h1>
                <p>Въведи пет символа и следи новите записи в реално време. Работи за частни клипбордове с валиден код.</p>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="/clipboard/share" class="share-code-form">
                <label for="code">Код за следене</label>
                <input id="code" name="code" value="<?= htmlspecialchars($code) ?>" class="form-control" maxlength="5" minlength="5" pattern="[A-Z2-9]{5}" placeholder="ABCDE" required>
                <button class="btn btn-primary">Стартирай живия поток</button>
            </form>
        </div>
    </section>

    <?php if (!empty($clipboard)): ?>
        <section id="shareDashboard" class="share-dashboard" data-share-code="<?= htmlspecialchars($clipboard['share_code'], ENT_QUOTES) ?>" data-last-id="<?= $lastId ?>" data-clipboard-id="<?= (int) $clipboard['id'] ?>">
            <div class="dashboard-header">
                <div class="dashboard-title">
                    <span class="live-pill"></span>
                    <div>
                        <small>Live sync active</small>
                        <h2><?= htmlspecialchars($clipboard['name']) ?></h2>
                    </div>
                </div>
                <div class="dashboard-meta">
                    <span class="dashboard-label">Код: <strong><?= htmlspecialchars($clipboard['share_code']) ?></strong></span>
                    <button id="copyClipboardLinkBtn" class="btn btn-outline btn-fit" data-url="/clipboard?id=<?= (int) $clipboard['id'] ?>">Копирай линк</button>
                </div>
            </div>

            <div class="clipboard-summary-card">
                <div class="clipboard-summary-info">
                    <span class="meta-badge <?= ($clipboard['visibility'] === 'private' ? 'badge-secondary' : 'badge-success') ?>">
                        <?= ($clipboard['visibility'] === 'private' ? 'Частен' : 'Публичен') ?> клипборд
                    </span>
                    <p><?= htmlspecialchars($clipboard['description'] ?? 'Няма описание.') ?></p>
                </div>
                <div class="clipboard-summary-stats">
                    <div>
                        <span>Последни записи</span>
                        <strong id="itemsCount"><?= count($items) ?></strong>
                    </div>
                    <div>
                        <span>Обновяване</span>
                        <strong id="liveStatusTime">Сега</strong>
                    </div>
                </div>
            </div>

            <div class="feed-toolbar">
                <div class="feed-status">
                    <span class="feed-chip">Live</span>
                    <span id="newItemsCount" class="feed-new-items">0 нови</span>
                </div>
                <div class="feed-actions">
                    <a class="btn btn-outline" href="/clipboard?id=<?= (int) $clipboard['id'] ?>">Отвори пълния клипборд</a>
                </div>
            </div>

            <div id="shareFeed" class="share-feed">
                <?php if (empty($items)): ?>
                    <div class="empty-state">
                        <span class="material-symbols-outlined">sync</span>
                        <h3>Очакване на първия запис</h3>
                        <p>Ще получаваш нови записи тук веднага щом бъдат добавени.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <article class="feed-card <?= htmlspecialchars($item['display_type']) ?>">
                            <div class="feed-card-header">
                                <div>
                                    <span class="feed-badge type-<?= htmlspecialchars($item['display_type']) ?>"><?= htmlspecialchars($item['type_label']) ?></span>
                                    <span class="feed-time"><?= htmlspecialchars($item['time_ago']) ?></span>
                                </div>
                                <button class="btn btn-outline btn-icon copy-item-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Копирай">copy</button>
                            </div>
                            <div class="feed-card-body">
                                <?php if ($item['display_type'] === 'image'): ?>
                                    <img class="feed-image" src="<?= htmlspecialchars($item['content']) ?>" alt="Shared image" loading="lazy">
                                <?php elseif ($item['display_type'] === 'file'): ?>
                                    <div class="feed-file-card">
                                        <span class="material-symbols-outlined">download</span>
                                        <a href="<?= htmlspecialchars($item['content']) ?>" target="_blank">Свали файл</a>
                                    </div>
                                <?php elseif ($item['display_type'] === 'link'): ?>
                                    <a class="feed-link" href="<?= htmlspecialchars($item['content']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($item['content']) ?></a>
                                <?php elseif ($item['display_type'] === 'code'): ?>
                                    <pre><?= htmlspecialchars($item['content']) ?></pre>
                                <?php else: ?>
                                    <p><?= nl2br(htmlspecialchars($item['content'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="toastContainer" class="toast-container"></div>
        </section>
    <?php endif; ?>
</div>
