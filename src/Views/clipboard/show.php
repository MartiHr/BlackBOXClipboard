        <div class="clipboard-panel">
            <div class="breadcrumb">
                <a href="/">Клипборди</a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <span class="breadcrumb-current"><?= htmlspecialchars($clipboard['name']) ?></span>
            </div>

            <div class="clipboard-summary">
                <div>
                    <span class="channel-status <?= $clipboard['visibility'] === 'private' ? 'channel-status-standby' : '' ?>">
                        <?= $clipboard['visibility'] === 'private' ? 'Частен' : 'Публичен' ?> клипборд
                    </span>
                    <h1><?= htmlspecialchars($clipboard['name']) ?></h1>
                    <?php if (!empty($clipboard['description'])): ?>
                    <p class="clipboard-description"><?= htmlspecialchars($clipboard['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="clipboard-meta-grid">
                <div class="meta-card">
                    <span class="meta-label">Режим</span>
                    <strong><?= ($clipboard['mode'] ?? 'multi') === 'multi' ? 'Множествен' : 'Единичен' ?></strong>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Създаден</span>
                    <strong><?= htmlspecialchars(date('j M Y', strtotime($clipboard['created_at']))) ?></strong>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Обекти</span>
                    <strong><?= count($items) ?></strong>
                </div>
                <?php if (($clipboard['visibility'] ?? 'private') === 'public'): ?>
                <button class="btn btn-outline btn-fit share-url-btn" title="Копирай връзката">
                    <span class="material-symbols-outlined">share</span> Сподели
                </button>
                <?php if (!empty($clipboard['share_code'])): ?>
                <button class="btn btn-outline btn-fit share-code-btn" title="Копирай кода" data-share-code="<?= htmlspecialchars($clipboard['share_code'], ENT_QUOTES) ?>">
                    <span class="material-symbols-outlined">key</span> Код
                    <span class="share-code-badge"><?= htmlspecialchars($clipboard['share_code']) ?></span>
                </button>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="subscribe-group">
                    <button class="btn <?= $isSubscribed ? 'btn-primary' : 'btn-outline' ?> btn-fit subscribe-btn" data-clipboard-id="<?= (int) $clipboard['id'] ?>">
                        <span class="material-symbols-outlined"><?= $isSubscribed ? 'notifications_active' : 'notifications' ?></span>
                        <span class="subscribe-label"><?= $isSubscribed ? 'Абониран' : 'Абонирай се' ?></span>
                    </button>
                    <label class="switch-toggle" style="display:<?= $isSubscribed ? 'inline-flex' : 'none' ?>">
                        <input type="checkbox" class="email-toggle" <?= $notifyEmail ? 'checked' : '' ?>>
                        <span class="switch-slider"></span>
                        <span class="switch-label">Email</span>
                    </label>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="allowed-types-section">
                <span class="meta-label">Разрешени типове</span>
                <div class="allowed-types">
                    <?php if (!empty($allowedTypes)): ?>
                        <?php foreach ($allowedTypes as $type): ?>
                            <span class="type-badge"><?= htmlspecialchars($type['name']) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="type-badge">Всички типове</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($canPost): ?>
            <div class="input-section">
                <textarea id="pasteInput" placeholder="Пейстни текст, линк или код тук..."></textarea>

                <div id="filePreview" class="file-preview" style="display:none">
                    <img id="previewImage" class="file-preview-thumb" style="display:none" alt="preview">
                    <div id="previewIcon" class="file-preview-icon" style="display:none">
                        <span class="material-symbols-outlined" id="previewIconGlyph">description</span>
                    </div>
                    <div class="file-preview-info">
                        <span id="previewFileName"></span>
                        <span id="previewFileSize"></span>
                    </div>
                    <button type="button" id="removeFileBtn" class="btn btn-icon-only btn-outline" title="Премахни">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <input type="file" id="fileInput" accept="*/*" style="display:none">
                <div class="input-actions">
                    <div class="input-left-group">
                        <button class="btn btn-icon-only btn-outline" id="fileBtn" title="Качи файл">
                            <span class="material-symbols-outlined">attach_file</span>
                        </button>
                        <select id="typeSelector" class="type-selector">
                            <option value="auto">Автоматично</option>
                            <option value="text">Текст</option>
                            <option value="link">Линк</option>
                            <option value="code">Код</option>
                            <option value="image">Изображение</option>
                            <option value="file">Файл</option>
                        </select>
                        <select id="lifespanSelector" class="type-selector" title="Автоматично изтриване">
                            <option value="">Без срок</option>
                            <option value="60">1 минута</option>
                            <option value="600">10 минути</option>
                            <option value="3600">1 час</option>
                            <option value="14400">4 часа</option>
                            <option value="86400">1 ден</option>
                            <option value="604800">7 дни</option>
                            <option value="2592000">30 дни</option>
                        </select>
                        <span class="input-type-hint" id="typeHint">Определяне на типа...</span>
                    </div>
                    <button class="btn btn-primary btn-fit" id="shareBtn">Сподели</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="items-section">
                <div class="items-header">
                    <h2>Последни споделяния</h2>
                    <span class="items-count"><?= count($items) ?> записа</span>
                </div>

                <?php if (empty($items)): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">content_paste</span>
                    <h3>Все още няма споделяния</h3>
                    <p><?= $canPost ? 'Постави нещо в полето отгоре и натисни "Сподели".' : 'Все още няма споделяния в този публичен клипборд.' ?></p>
                </div>
                <?php else: ?>
                <div class="items-feed">
                    <?php foreach ($items as $item): ?>
                    <article class="item-card <?= $item['display_type'] === 'code' ? 'code-card' : '' ?>">
                        <div class="item-card-header">
                            <div class="item-card-meta">
                                <span class="item-type-badge type-<?= htmlspecialchars($item['display_type']) ?>">
                                    <?= htmlspecialchars($item['type_label']) ?>
                                </span>
                                <span class="item-time"><?= htmlspecialchars($item['time_ago']) ?></span>
                            </div>
                            <div class="item-card-actions">
                                <?php if ($item['display_type'] === 'image' || $item['display_type'] === 'file'): ?>
                                <button class="btn btn-icon-only btn-outline download-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Свали">
                                    <span class="material-symbols-outlined">download</span>
                                </button>
                                <?php if (str_starts_with($item['content'], 'http')): ?>
                                <button class="btn btn-icon-only btn-outline url-copy-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Копирай URL">
                                    <span class="material-symbols-outlined">link</span>
                                </button>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($isOwner || $isAdmin): ?>
                                <button class="btn btn-icon-only btn-outline delete-item-btn" data-id="<?= (int) $item['id'] ?>" title="Изтрий">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                                <?php endif; ?>
                                <button class="copy-btn" data-type="<?= htmlspecialchars($item['display_type']) ?>" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>">Копирай</button>
                            </div>
                        </div>
                        <div class="item-card-body">
                            <?php if ($item['display_type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars($item['content']) ?>" alt="Shared image" class="item-content-image" loading="lazy">
                            <?php elseif ($item['display_type'] === 'file'): ?>
                                <div class="item-content-file">
                                    <span class="material-symbols-outlined file-card-icon">insert_drive_file</span>
                                    <div>
                                        <div class="file-name-label"><?= htmlspecialchars($item['type_label']) ?> документ</div>
                                        <a href="<?= htmlspecialchars($item['content']) ?>" class="file-download-link" target="_blank">Изтегли файл</a>
                                    </div>
                                </div>
                            <?php elseif ($item['display_type'] === 'link'): ?>
                                <a href="<?= htmlspecialchars($item['content']) ?>" target="_blank" rel="noopener" class="item-content-link">
                                    <span class="material-symbols-outlined link-icon">link</span>
                                    <?= htmlspecialchars($item['content']) ?>
                                </a>
                            <?php elseif ($item['display_type'] === 'code'): ?>
                                <pre class="item-content-code"><?= htmlspecialchars($item['content']) ?></pre>
                            <?php else: ?>
                                <div class="item-content-text"><?= htmlspecialchars($item['content']) ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="delete-modal" class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <span class="material-symbols-outlined modal-icon">delete</span>
                    <h3>Изтриване на запис</h3>
                </div>
                <p class="modal-body">Сигурни ли сте, че искате да изтриете този запис? Това действие не може да бъде отменено.</p>
                <div class="modal-actions">
                    <button id="delete-cancel-btn" class="btn btn-outline">Отказ</button>
                    <button id="delete-confirm-btn" class="btn btn-danger">Изтрий</button>
                </div>
            </div>
        </div>

        <div id="validation-modal" class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <span class="material-symbols-outlined modal-icon">error</span>
                    <h3>Тип не е разрешен</h3>
                </div>
                <p class="modal-body" id="validation-error-text"></p>
                <div class="modal-actions">
                    <button id="validation-close-btn" class="btn btn-primary">OK</button>
                </div>
            </div>
        </div>

        <script>
        window._clipboardData = {
            allowedTypes: <?= !empty($allowedTypes) ? json_encode(array_column($allowedTypes, 'category')) : '[]' ?>,
            clipboardId: <?= (int) $clipboard['id'] ?>
        };
        </script>
