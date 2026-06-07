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
                </div>                <?php if (($clipboard['visibility'] ?? 'private') === 'public'): ?>
                <button class="btn btn-outline btn-fit share-url-btn" title="Копирай връзката">
                    <span class="material-symbols-outlined">share</span> Сподели
                </button>
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

        <script>
            const pasteInput = document.getElementById('pasteInput');
            const typeHint = document.getElementById('typeHint');
            const shareBtn = document.getElementById('shareBtn');
            const fileBtn = document.getElementById('fileBtn');
            const fileInput = document.getElementById('fileInput');
            const typeSelector = document.getElementById('typeSelector');
            const filePreview = document.getElementById('filePreview');
            const previewImage = document.getElementById('previewImage');
            const previewIcon = document.getElementById('previewIcon');
            const previewIconGlyph = document.getElementById('previewIconGlyph');
            const previewFileName = document.getElementById('previewFileName');
            const previewFileSize = document.getElementById('previewFileSize');
            const removeFileBtn = document.getElementById('removeFileBtn');

            var currentFileData = null;
            var currentFileName = '';
            var currentFileType = '';

            // Allowed types from PHP
            const allowedTypes = <?= !empty($allowedTypes) ? json_encode(array_column($allowedTypes, 'category')) : '[]' ?>;
            const hasRestrictions = allowedTypes.length > 0;

            function detectType(text) {
                const t = text.trim();
                if (!t) return '';
                if (/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|svg|bmp)(\?.*)?$/i.test(t) || t.startsWith('data:image/')) return 'image';
                if (t.startsWith('data:')) return 'file';
                if (/^https?:\/\//i.test(t)) return 'link';
                if (/\b(function|class|if|else|for|while|foreach|switch|case|return|echo|print|require|include)\s*\(/i.test(t) ||
                    /[{};]/.test(t) || /<\?php/.test(t) || /^(import|export|const|let|var|def|lambda)\s/i.test(t)) return 'code';
                return 'text';
            }

            function detectFileType(fileName, mimeType) {
                const name = (fileName || '').toLowerCase();
                const ext = name.includes('.') ? name.split('.').pop() : '';
                const codeExts = ['php','js','ts','tsx','jsx','html','css','sql','json','xml','md','yaml','yml','py','java','c','cpp','cs','rb','go','rs','sh','bash','ps1','swift','dart','kt','kts'];

                if (mimeType && mimeType.startsWith('image/')) {
                    return 'image';
                }
                if (mimeType && (/\b(json|xml|javascript|x-javascript|x-php|x-shellscript|x-sh|x-python|x-java-source|x-csrc|x-c\+\+src)\b/i.test(mimeType) || mimeType.startsWith('text/'))) {
                    if (codeExts.includes(ext) || /json|xml|html|css|js|ts|jsx|tsx|php|py|java|c|cpp|cs|rb|go|rs|sh|bash|ps1|swift|dart|kt|kts/.test(ext)) {
                        return 'code';
                    }
                    return 'text';
                }
                if (codeExts.includes(ext)) {
                    return 'code';
                }
                if (['txt','md','log','ini','cfg','conf'].includes(ext)) {
                    return 'text';
                }
                if (['jpg','jpeg','png','gif','webp','svg','bmp'].includes(ext)) {
                    return 'image';
                }
                return 'file';
            }

            const typeLabels = { text: 'Текст', link: 'Линк', code: 'Код', image: 'Изображение', file: 'Файл' };

            function updateTypeHint() {
                if (currentFileData) {
                    const fileType = detectFileType(currentFileName, currentFileType);
                    typeHint.textContent = 'Качен файл: ' + currentFileName + ' (' + typeLabels[fileType] + ')';
                    return;
                }
                const type = detectType(pasteInput.value);
                typeHint.textContent = type ? 'Разпознат тип: ' + typeLabels[type] : 'Определяне на типа...';
            }

            pasteInput.addEventListener('input', function() {
                updateTypeHint();
            });

            fileBtn.addEventListener('click', function() {
                fileInput.click();
            });

            function showFilePreview(file, dataUri) {
                currentFileData = dataUri;
                currentFileName = file.name;
                currentFileType = file.type;

                pasteInput.style.display = 'none';
                filePreview.style.display = 'flex';

                previewFileName.textContent = file.name;
                var size = file.size;
                if (size < 1024) previewFileSize.textContent = size + ' B';
                else if (size < 1048576) previewFileSize.textContent = (size / 1024).toFixed(1) + ' KB';
                else previewFileSize.textContent = (size / 1048576).toFixed(1) + ' MB';

                if (file.type.startsWith('image/')) {
                    previewImage.style.display = 'block';
                    previewIcon.style.display = 'none';
                    previewImage.src = dataUri;
                } else {
                    previewImage.style.display = 'none';
                    previewImage.src = '';
                    previewIcon.style.display = 'flex';
                    if (file.type === 'application/pdf') previewIconGlyph.textContent = 'picture_as_pdf';
                    else if (file.type.startsWith('text/') || file.type.includes('json') || file.type.includes('xml')) previewIconGlyph.textContent = 'description';
                    else if (file.type.includes('zip') || file.type.includes('rar') || file.type.includes('tar')) previewIconGlyph.textContent = 'folder_zip';
                    else previewIconGlyph.textContent = 'insert_drive_file';
                }

                typeSelector.value = 'auto';
                updateTypeHint();
            }

            function clearFilePreview() {
                currentFileData = null;
                currentFileName = '';
                currentFileType = '';
                filePreview.style.display = 'none';
                pasteInput.style.display = '';
                previewImage.src = '';
                previewImage.style.display = 'none';
                previewIcon.style.display = 'none';
                updateTypeHint();
            }

            function readFile(file) {
                if (file.type.startsWith('text/') || file.type === 'application/json' || file.type === 'application/xml') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        showFilePreview(file, e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        showFilePreview(file, e.target.result);
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

            removeFileBtn.addEventListener('click', clearFilePreview);

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
                var content, type, detectedType;

                if (currentFileData) {
                    content = currentFileData;
                    if (typeSelector.value === 'auto') {
                        detectedType = detectFileType(currentFileName, currentFileType);
                        type = null;
                    } else {
                        type = typeSelector.value;
                        detectedType = type;
                    }
                } else {
                    content = pasteInput.value.trim();
                    if (!content) return;
                    detectedType = detectType(content);
                    type = typeSelector.value === 'auto' ? null : typeSelector.value;
                }

                // Validate against allowed types
                if (hasRestrictions && !allowedTypes.includes(detectedType)) {
                    document.getElementById('validation-error-text').textContent = 'Тип "' + detectedType + '" не е разрешен. Разрешени типове: ' + allowedTypes.join(', ');
                    document.getElementById('validation-modal').classList.add('open');
                    return;
                }

                shareBtn.disabled = true;
                shareBtn.textContent = 'Изпращане...';

                var payload = {
                    clipboard_id: <?= (int) $clipboard['id'] ?>,
                    content: content
                };
                if (type) payload.type = type;

                fetch('/clipboard/item/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
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

            document.getElementById('validation-close-btn').addEventListener('click', function() {
                document.getElementById('validation-modal').classList.remove('open');
            });

            document.getElementById('validation-modal').addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('open');
            });
        </script>
