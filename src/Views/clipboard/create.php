        <div class="create-container">
            <div class="breadcrumb">
                <a href="/">Клипборди</a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <span class="breadcrumb-current">Нов клипборд</span>
            </div>

            <div class="create-card">
                <div class="create-card-header">
                    <h1>Създаване на клипборд</h1>
                    <p>Конфигурирайте работната среда за безпроблемно синхронизиране на клипборда.</p>
                </div>

                <form id="createForm">
                    <div class="form-group">
                        <label for="name">Име на клипборд</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="напр. Project Phoenix Assets" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea id="description" name="description" class="form-control create-textarea" placeholder="Опишете предназначението на този клипборд..." rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="visibility">Видимост</label>
                            <select id="visibility" name="visibility" class="form-control">
                                <option value="private">Частен</option>
                                <option value="public">Публичен</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Режим</label>
                            <div class="mode-toggle">
                                <button type="button" class="mode-option active" data-value="single">Единичен</button>
                                <button type="button" class="mode-option" data-value="multi">Множествен</button>
                                <input type="hidden" name="mode" id="modeInput" value="single">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Позволени типове ресурси</label>
                        <div class="resource-grid">
                            <?php if (!empty($resourceTypes)): ?>
                                <?php foreach ($resourceTypes as $type): ?>
                                <label class="resource-option" style="border-color:">
                                    <input type="checkbox" name="types[]" value="<?= htmlspecialchars($type['id']) ?>">
                                    <div class="resource-info">
                                        <span class="resource-label"><?= htmlspecialchars($type['name']) ?></span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <label class="resource-option">
                                    <input type="checkbox" name="types[]" value="1">
                                    <div class="resource-info">
                                        <span class="resource-label">Текст</span>
                                        <span class="resource-desc">Бележки и фрагменти</span>
                                    </div>
                                </label>
                                <label class="resource-option">
                                    <input type="checkbox" name="types[]" value="2">
                                    <div class="resource-info">
                                        <span class="resource-label">Изображение</span>
                                        <span class="resource-desc">Визуални файлове</span>
                                    </div>
                                </label>
                                <label class="resource-option">
                                    <input type="checkbox" name="types[]" value="3">
                                    <div class="resource-info">
                                        <span class="resource-label">Код</span>
                                        <span class="resource-desc">Форматирани скриптове</span>
                                    </div>
                                </label>
                                <label class="resource-option">
                                    <input type="checkbox" name="types[]" value="4">
                                    <div class="resource-info">
                                        <span class="resource-label">Файл</span>
                                        <span class="resource-desc">Основни документи</span>
                                    </div>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="safety-notice">
                        <span class="material-symbols-outlined safety-icon">verified_user</span>
                        <p class="safety-text"><strong>Сигурно предаване:</strong> Съдържанието в този клипборд е достъпно само за потребители с необходимите права за достъп. Всички действия се валидират от системата и се записват в историята на клипборда.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-fit">Създай клипборд</button>
                        <a href="/" class="btn btn-outline btn-fit">Отказ</a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.querySelectorAll('.mode-option').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.mode-option').forEach(b => {
                        b.classList.remove('active');
                    });
                    this.classList.add('active');
                    document.getElementById('modeInput').value = this.dataset.value;
                });
            });

            document.getElementById('createForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const checked = Array.from(this.querySelectorAll('input[name="types[]"]:checked')).map(c => parseInt(c.value));
                fetch('/clipboard/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: this.name.value,
                        description: this.description.value,
                        visibility: this.visibility.value,
                        mode: this.modeInput.value,
                        allowed_resource_types: checked
                    })
                }).then(r => r.json()).then(d => {
                    if (d.success) window.location.href = '/';
                    else alert(d.error || 'Грешка при създаване');
                });
            });
        </script>
