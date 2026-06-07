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
                <?php endif; ?>
            </div>

            <?php if ($canPost): ?>
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
                                <?php if ($item['display_type'] === 'image'): ?>
                                <button class="btn btn-icon-only btn-outline download-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Свали">
                                    <span class="material-symbols-outlined">download</span>
                                </button>
                                <?php if (str_starts_with($item['content'], 'http') || str_starts_with($item['content'], '/file')): ?>
                                <button class="btn btn-icon-only btn-outline url-copy-btn" data-content="<?= htmlspecialchars($item['content'], ENT_QUOTES) ?>" title="Копирай URL">
                                    <span class="material-symbols-outlined">link</span>
                                </button>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($isOwner): ?>
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

            function readFile(file) {
                if (file.type.startsWith('text/') || file.type === 'application/json' || file.type === 'application/xml') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        pasteInput.value = e.target.result;
                        pasteInput.dispatchEvent(new Event('input'));
                    };
                    reader.readAsText(file);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        pasteInput.value = e.target.result;
                        pasteInput.dispatchEvent(new Event('input'));
                    };
                    reader.readAsDataURL(file);
                }
            }

            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                readFile(file);
                this.value = '';
            });

            var dropZone = document.querySelector('.input-section');

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            dropZone.addEventListener('dragleave', function(e) {
                this.classList.remove('drag-over');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                const file = e.dataTransfer.files[0];
                if (file) readFile(file);
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

            var pendingDeleteId = null;

            function showDeleteModal(itemId) {
                pendingDeleteId = itemId;
                document.getElementById('delete-modal').classList.add('open');
            }

            function hideDeleteModal() {
                document.getElementById('delete-modal').classList.remove('open');
                pendingDeleteId = null;
            }

            var urlShareBtn = document.querySelector('.share-url-btn');
            if (urlShareBtn) {
                urlShareBtn.addEventListener('click', function() {
                    var url = window.location.href;
                    navigator.clipboard.writeText(url).then(function() {
                        var orig = this.innerHTML;
                        this.innerHTML = '<span class="material-symbols-outlined">check</span> Копиран!';
                        var self = this;
                        setTimeout(function() { self.innerHTML = orig; }, 2000);
                    }.bind(this));
                });
            }

            document.querySelectorAll('.delete-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showDeleteModal(this.dataset.id);
                });
            });
        </script>

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

        <script>
            document.getElementById('delete-confirm-btn').addEventListener('click', function() {
                if (!pendingDeleteId) return;

                fetch('/clipboard/item/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: pendingDeleteId })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        var card = document.querySelector('.delete-item-btn[data-id="' + pendingDeleteId + '"]');
                        if (card) card.closest('.item-card').remove();
                        hideDeleteModal();
                    } else {
                        alert(d.error || 'Грешка при изтриване');
                        hideDeleteModal();
                    }
                });
            });

            document.getElementById('delete-cancel-btn').addEventListener('click', hideDeleteModal);

            document.getElementById('delete-modal').addEventListener('click', function(e) {
                if (e.target === this) hideDeleteModal();
            });
        </script>
