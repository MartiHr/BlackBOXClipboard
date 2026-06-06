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
            </div>

            <div class="input-section">
                <textarea id="pasteInput" placeholder="Пейстни текст, линк или код тук..."></textarea>
                <input type="file" id="fileInput" accept="text/*,image/*,.pdf,.zip,.csv,.json,.xml,.sql" style="display:none">
                <div class="input-actions">
                    <div class="input-left-group">
                        <button class="btn btn-icon-only btn-outline" id="fileBtn" title="Качи файл">
                            <span class="material-symbols-outlined">attach_file</span>
                        </button>
                        <span class="input-type-hint" id="typeHint">Определяне на типа...</span>
                    </div>
                    <button class="btn btn-primary btn-fit" id="shareBtn">Сподели</button>
                </div>
            </div>

            <div class="items-section">
                <div class="items-header">
                    <h2>Последни споделяния</h2>
                    <span class="items-count"><?= count($items) ?> записа</span>
                </div>

                <?php if (empty($items)): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">content_paste</span>
                    <h3>Все още няма споделяния</h3>
                    <p>Постави нещо в полето отгоре и натисни "Сподели".</p>
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
                                <?php if ($item['display_type'] === 'image'): ?>
                                <button class="btn btn-icon-only btn-outline download-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Свали">
                                    <span class="material-symbols-outlined">download</span>
                                </button>
                                <?php if (str_starts_with($item['content'], 'http')): ?>
                                <button class="btn btn-icon-only btn-outline url-copy-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Копирай URL">
                                    <span class="material-symbols-outlined">link</span>
                                </button>
                                <?php endif; ?>
                                <?php endif; ?>
                                <button class="copy-btn" data-type="<?= htmlspecialchars($item['display_type']) ?>" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>">Копирай</button>
                            </div>
                        </div>
                        <div class="item-card-body">
                            <?php if ($item['display_type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars($item['content']) ?>" alt="Shared image" class="item-content-image" loading="lazy">
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

        <script>
            const pasteInput = document.getElementById('pasteInput');
            const typeHint = document.getElementById('typeHint');
            const shareBtn = document.getElementById('shareBtn');
            const fileBtn = document.getElementById('fileBtn');
            const fileInput = document.getElementById('fileInput');

            function detectType(text) {
                const t = text.trim();
                if (!t) return '';
                if (/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|svg|bmp)(\?.*)?$/i.test(t) || t.startsWith('data:image/')) return 'image';
                if (/^https?:\/\//i.test(t)) return 'link';
                if (/\b(function|class|if|else|for|while|foreach|switch|case|return|echo|print|require|include)\s*\(/i.test(t) ||
                    /[{};]/.test(t) || /<\?php/.test(t) || /^(import|export|const|let|var|def|lambda)\s/i.test(t)) return 'code';
                return 'text';
            }

            const typeLabels = { text: 'Текст', link: 'Линк', code: 'Код', image: 'Изображение' };

            pasteInput.addEventListener('input', function() {
                const type = detectType(this.value);
                typeHint.textContent = type ? 'Разпознат тип: ' + typeLabels[type] : 'Определяне на типа...';
            });

            fileBtn.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                if (file.type.startsWith('text/') || file.type === 'application/json' || file.type === 'application/xml') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        pasteInput.value = e.target.result;
                        pasteInput.dispatchEvent(new Event('input'));
                    };
                    reader.readAsText(file);
                } else if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        pasteInput.value = e.target.result;
                        pasteInput.dispatchEvent(new Event('input'));
                    };
                    reader.readAsDataURL(file);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        pasteInput.value = e.target.result;
                        pasteInput.dispatchEvent(new Event('input'));
                    };
                    reader.readAsDataURL(file);
                }

                this.value = '';
            });

            shareBtn.addEventListener('click', function() {
                const content = pasteInput.value.trim();
                if (!content) return;

                shareBtn.disabled = true;
                shareBtn.textContent = 'Изпращане...';

                const clipboardId = <?= (int) $clipboard['id'] ?>;

                fetch('/clipboard/item/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        clipboard_id: clipboardId,
                        content: content
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        location.reload();
                    } else {
                        alert(d.error || 'Грешка при споделяне');
                        shareBtn.disabled = false;
                        shareBtn.textContent = 'Сподели';
                    }
                })
                .catch(() => {
                    alert('Грешка при свързване със сървъра');
                    shareBtn.disabled = false;
                    shareBtn.textContent = 'Сподели';
                });
            });

            function copyImageToClipboard(url, btn, done) {
                fetch(url)
                    .then(r => r.blob())
                    .then(blob => navigator.clipboard.write([
                        new ClipboardItem({ [blob.type]: blob })
                    ]))
                    .then(done)
                    .catch(() => {
                        var img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = function() {
                            var c = document.createElement('canvas');
                            c.width = img.width;
                            c.height = img.height;
                            c.getContext('2d').drawImage(img, 0, 0);
                            c.toBlob(function(blob) {
                                if (blob) {
                                    navigator.clipboard.write([
                                        new ClipboardItem({ [blob.type]: blob })
                                    ]).then(done).catch(fallback);
                                } else {
                                    fallback();
                                }
                            });
                        };
                        img.onerror = fallback;
                        img.src = url;
                    });

                function fallback() {
                    navigator.clipboard.writeText(url).then(done);
                }
            }

            document.querySelectorAll('.copy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const content = this.dataset.content;
                    const type = this.dataset.type;
                    const self = this;
                    const done = function() {
                        self.textContent = 'Копирано!';
                        self.classList.add('copied');
                        setTimeout(function() {
                            self.textContent = 'Копирай';
                            self.classList.remove('copied');
                        }, 1500);
                    };

                    if (type === 'image') {
                        copyImageToClipboard(content, self, done);
                    } else {
                        navigator.clipboard.writeText(content).then(done);
                    }
                });
            });

            document.querySelectorAll('.url-copy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const self = this;
                    navigator.clipboard.writeText(this.dataset.content).then(function() {
                        self.innerHTML = '<span class="material-symbols-outlined">check</span>';
                        setTimeout(function() {
                            self.innerHTML = '<span class="material-symbols-outlined">link</span>';
                        }, 1500);
                    });
                });
            });

            document.querySelectorAll('.download-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.dataset.content;

                    if (url.startsWith('data:')) {
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'blackbox-image-' + Date.now();
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    } else {
                        fetch(url)
                            .then(r => r.blob())
                            .then(blob => {
                                const a = document.createElement('a');
                                a.href = URL.createObjectURL(blob);
                                a.download = 'blackbox-image-' + Date.now();
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(a.href);
                            })
                            .catch(() => {
                                window.open(url, '_blank');
                            });
                    }
                });
            });
        </script>
