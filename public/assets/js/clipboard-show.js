document.addEventListener('DOMContentLoaded', function() {
    var cd = window._clipboardData || {};
    var allowedTypes = cd.allowedTypes || [];
    var hasRestrictions = allowedTypes.length > 0;
    var clipboardId = cd.clipboardId;

    var pasteInput = document.getElementById('pasteInput');
    var typeHint = document.getElementById('typeHint');
    var shareBtn = document.getElementById('shareBtn');
    var fileBtn = document.getElementById('fileBtn');
    var fileInput = document.getElementById('fileInput');
    var typeSelector = document.getElementById('typeSelector');
    var filePreview = document.getElementById('filePreview');
    var previewImage = document.getElementById('previewImage');
    var previewIcon = document.getElementById('previewIcon');
    var previewIconGlyph = document.getElementById('previewIconGlyph');
    var previewFileName = document.getElementById('previewFileName');
    var previewFileSize = document.getElementById('previewFileSize');
    var removeFileBtn = document.getElementById('removeFileBtn');

    var currentFileData = null;
    var currentFileName = '';
    var currentFileType = '';
    var pendingDeleteId = null;

    function detectType(text) {
        var t = text.trim();
        if (!t) return '';
        if (/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|svg|bmp)(\?.*)?$/i.test(t) || t.startsWith('data:image/')) return 'image';
        if (t.startsWith('data:')) return 'file';
        if (/^https?:\/\//i.test(t)) return 'link';
        if (/\b(function|class|if|else|for|while|foreach|switch|case|return|echo|print|require|include)\s*\(/i.test(t) ||
            /[{};]/.test(t) || /<\?php/.test(t) || /^(import|export|const|let|var|def|lambda)\s/i.test(t)) return 'code';
        return 'text';
    }

    function detectFileType(fileName, mimeType) {
        var name = (fileName || '').toLowerCase();
        var ext = name.includes('.') ? name.split('.').pop() : '';
        var codeExts = ['php','js','ts','tsx','jsx','html','css','sql','json','xml','md','yaml','yml','py','java','c','cpp','cs','rb','go','rs','sh','bash','ps1','swift','dart','kt','kts'];

        if (mimeType && mimeType.startsWith('image/')) return 'image';
        if (mimeType && (/\b(json|xml|javascript|x-javascript|x-php|x-shellscript|x-sh|x-python|x-java-source|x-csrc|x-c\+\+src)\b/i.test(mimeType) || mimeType.startsWith('text/'))) {
            if (codeExts.includes(ext) || /json|xml|html|css|js|ts|jsx|tsx|php|py|java|c|cpp|cs|rb|go|rs|sh|bash|ps1|swift|dart|kt|kts/.test(ext)) return 'code';
            return 'text';
        }
        if (codeExts.includes(ext)) return 'code';
        if (['txt','md','log','ini','cfg','conf'].includes(ext)) return 'text';
        if (['jpg','jpeg','png','gif','webp','svg','bmp'].includes(ext)) return 'image';
        return 'file';
    }

    var typeLabels = { text: 'Текст', link: 'Линк', code: 'Код', image: 'Изображение', file: 'Файл' };

    function updateTypeHint() {
        if (currentFileData) {
            var fileType = detectFileType(currentFileName, currentFileType);
            typeHint.textContent = 'Качен файл: ' + currentFileName + ' (' + typeLabels[fileType] + ')';
            return;
        }
        var type = detectType(pasteInput.value);
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
        var reader = new FileReader();
        reader.onload = function(e) {
            showFilePreview(file, e.target.result);
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        readFile(file);
        this.value = '';
    });

    removeFileBtn.addEventListener('click', clearFilePreview);

    var dropZone = document.querySelector('.input-section');
    if (dropZone) {
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
            var file = e.dataTransfer.files[0];
            if (file) readFile(file);
        });
    }

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

        if (hasRestrictions && !allowedTypes.includes(detectedType)) {
            document.getElementById('validation-error-text').textContent = 'Тип "' + detectedType + '" не е разрешен. Разрешени типове: ' + allowedTypes.join(', ');
            document.getElementById('validation-modal').classList.add('open');
            return;
        }

        shareBtn.disabled = true;
        shareBtn.textContent = 'Изпращане...';

        var lifespanEl = document.getElementById('lifespanSelector');
        var expiresIn = lifespanEl ? lifespanEl.value : '';

        var payload = {
            clipboard_id: clipboardId,
            content: content
        };
        if (type) payload.type = type;
        if (expiresIn) payload.expires_in = parseInt(expiresIn);

        fetch('/clipboard/item/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                location.reload();
            } else {
                alert(d.error || 'Грешка при споделяне');
                shareBtn.disabled = false;
                shareBtn.textContent = 'Сподели';
            }
        })
        .catch(function() {
            alert('Грешка при свързване със сървъра');
            shareBtn.disabled = false;
            shareBtn.textContent = 'Сподели';
        });
    });

    function copyImageToClipboard(url, btn, done) {
        fetch(url)
            .then(function(r) { return r.blob(); })
            .then(function(blob) {
                return navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })]);
            })
            .then(done)
            .catch(function() {
                var img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    var c = document.createElement('canvas');
                    c.width = img.width;
                    c.height = img.height;
                    c.getContext('2d').drawImage(img, 0, 0);
                    c.toBlob(function(blob) {
                        if (blob) {
                            navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })]).then(done).catch(fallback);
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

    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var content = this.dataset.content;
            var type = this.dataset.type;
            var self = this;
            var done = function() {
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

    document.querySelectorAll('.url-copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var self = this;
            navigator.clipboard.writeText(this.dataset.content).then(function() {
                self.innerHTML = '<span class="material-symbols-outlined">check</span>';
                setTimeout(function() {
                    self.innerHTML = '<span class="material-symbols-outlined">link</span>';
                }, 1500);
            });
        });
    });

    document.querySelectorAll('.download-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.dataset.content;

            if (url.startsWith('data:')) {
                var a = document.createElement('a');
                a.href = url;
                a.download = 'blackbox-image-' + Date.now();
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                fetch(url)
                    .then(function(r) { return r.blob(); })
                    .then(function(blob) {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'blackbox-image-' + Date.now();
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(a.href);
                    })
                    .catch(function() {
                        window.open(url, '_blank');
                    });
            }
        });
    });

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
                var orig = urlShareBtn.innerHTML;
                urlShareBtn.innerHTML = '<span class="material-symbols-outlined">check</span> Копирано!';
                urlShareBtn.classList.add('copied');
                setTimeout(function() {
                    urlShareBtn.innerHTML = orig;
                    urlShareBtn.classList.remove('copied');
                }, 1600);
            });
        });
    }

    var shareCodeButton = document.querySelector('.share-code-btn');
    if (shareCodeButton) {
        shareCodeButton.addEventListener('click', function() {
            var code = this.dataset.shareCode || '';
            if (!code) return;
            navigator.clipboard.writeText(code).then(function() {
                var orig = shareCodeButton.innerHTML;
                shareCodeButton.innerHTML = '<span class="material-symbols-outlined">check</span> Копирано!';
                shareCodeButton.classList.add('copied');
                setTimeout(function() {
                    shareCodeButton.innerHTML = orig;
                    shareCodeButton.classList.remove('copied');
                }, 1600);
            });
        });
    }

    document.querySelectorAll('.delete-item-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            showDeleteModal(this.dataset.id);
        });
    });

    document.getElementById('delete-confirm-btn').addEventListener('click', function() {
        if (!pendingDeleteId) return;

        var btn = this;
        btn.disabled = true;

        fetch('/clipboard/item/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: pendingDeleteId })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                var card = document.querySelector('.delete-item-btn[data-id="' + pendingDeleteId + '"]');
                if (card) {
                    card.closest('.item-card').remove();
                    var countEl = document.querySelector('.items-count');
                    if (countEl) {
                        var m = countEl.textContent.match(/(\d+)/);
                        if (m) {
                            var n = parseInt(m[1]) - 1;
                            countEl.textContent = n + ' ' + (n === 1 ? 'запис' : 'записа');
                        }
                    }
                }
                hideDeleteModal();
            } else {
                alert(d.error || 'Грешка при изтриване');
                hideDeleteModal();
                btn.disabled = false;
            }
        })
        .catch(function() {
            alert('Грешка при свързване със сървъра');
            hideDeleteModal();
            btn.disabled = false;
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
});
