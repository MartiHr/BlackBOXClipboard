        <div class="create-container">
            <div class="breadcrumb">
                <a href="/">Клипборди</a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <span class="breadcrumb-current">Клипборд</span>
            </div>

            <section class="clipboard-panel">
                <div class="clipboard-summary">
                    <div>
                        <span class="channel-status <?= $clipboard['visibility'] === 'private' ? 'channel-status-standby' : '' ?>">
                            <?= $clipboard['visibility'] === 'private' ? 'Частен' : 'Публичен' ?> клипборд
                        </span>
                        <h1><?= htmlspecialchars($clipboard['name']) ?></h1>
                        <p class="clipboard-description"><?= htmlspecialchars($clipboard['description']) ?></p>
                    </div>
                    <button id="openItemCreate" class="btn btn-primary btn-fit">Добави елемент</button>
                </div>

                <div class="clipboard-meta-grid">
                    <div class="meta-card">
                        <span class="meta-label">Режим</span>
                        <strong><?= $clipboard['mode'] === 'multi' ? 'Множествен' : 'Единичен' ?></strong>
                    </div>
                    <div class="meta-card">
                        <span class="meta-label">Създаден</span>
                        <strong><?= htmlspecialchars(date('j M Y', strtotime($clipboard['created_at']))) ?></strong>
                    </div>
                    <div class="meta-card">
                        <span class="meta-label">Обекти</span>
                        <strong><?= count($items) ?></strong>
                    </div>
                </div>

                <div class="items-section">
                    <div class="items-header">
                        <h2>Последни споделяния</h2>
                        <span class="items-count"><?= count($items) ?> записа</span>
                    </div>

                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                        <article class="item-card">
                            <div class="item-card-top">
                                <div>
                                    <span class="item-type"><?= htmlspecialchars($item['type']) ?></span>
                                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                                </div>
                                <span class="material-symbols-outlined">chevron_right</span>
                            </div>
                            <p class="item-summary"><?= htmlspecialchars($item['summary']) ?></p>
                            <div class="item-card-footer">
                                <span><?= htmlspecialchars($item['timestamp']) ?></span>
                                <span class="item-status <?= $item['status'] === 'Консумирано' ? 'status-muted' : '' ?>"><?= htmlspecialchars($item['status']) ?></span>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>

        <script>
            document.getElementById('openItemCreate').addEventListener('click', function () {
                window.location.href = '/clipboard/item/create?clipboard_id=<?= htmlspecialchars($clipboard['id']) ?>';
            });
        </script>