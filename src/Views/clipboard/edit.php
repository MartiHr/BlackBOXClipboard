        <div class="create-container">
            <div class="breadcrumb">
                <a href="/">Клипборди</a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <a href="/clipboard?id=<?= (int) $clipboard['id'] ?>"><?= htmlspecialchars($clipboard['name']) ?></a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <span class="breadcrumb-current">Редактиране</span>
            </div>

            <div class="create-card">
                <div class="create-card-header">
                    <h1>Редактиране на клипборд</h1>
                    <p>Променете настройките на клипборда.</p>
                </div>

                <form id="editForm">
                    <input type="hidden" name="id" value="<?= (int) $clipboard['id'] ?>">

                    <div class="form-group">
                        <label for="name">Име на клипборд</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($clipboard['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea id="description" name="description" class="form-control create-textarea" rows="3"><?= htmlspecialchars($clipboard['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="visibility">Видимост</label>
                            <select id="visibility" name="visibility" class="form-control">
                                <option value="private" <?= ($clipboard['visibility'] ?? 'private') === 'private' ? 'selected' : '' ?>>Частен</option>
                                <option value="public" <?= ($clipboard['visibility'] ?? 'private') === 'public' ? 'selected' : '' ?>>Публичен</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Режим</label>
                            <div class="mode-toggle">
                                <button type="button" class="mode-option <?= ($clipboard['mode'] ?? 'multi') === 'single' ? 'active' : '' ?>" data-value="single">Единичен</button>
                                <button type="button" class="mode-option <?= ($clipboard['mode'] ?? 'multi') === 'multi' ? 'active' : '' ?>" data-value="multi">Множествен</button>
                                <input type="hidden" name="mode" id="modeInput" value="<?= htmlspecialchars($clipboard['mode'] ?? 'multi') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Позволени типове ресурси</label>
                        <div class="resource-actions select-all-container">
                            <label class="resource-option">
                                <input type="checkbox" id="toggleResourceTypes">
                                <div class="resource-info">
                                    <span class="resource-label" id="toggleResourceTypesLabel">Избери всички</span>
                                </div>
                            </label>
                        </div>
                        <div class="resource-grid">
                            <?php if (!empty($allTypes)): ?>
                                <?php
                                $allowedIds = array_map(function($t) { return $t['id']; }, $allowedTypes ?? []);
                                ?>
                                <?php foreach ($allTypes as $type): ?>
                                <label class="resource-option">
                                    <input type="checkbox" name="types[]" value="<?= (int) $type['id'] ?>" <?= in_array((int) $type['id'], $allowedIds) ? 'checked' : '' ?>>
                                    <div class="resource-info">
                                        <span class="resource-label"><?= htmlspecialchars($type['name']) ?></span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-fit">Запази</button>
                        <a href="/clipboard?id=<?= (int) $clipboard['id'] ?>" class="btn btn-outline btn-fit">Отказ</a>
                    </div>
                </form>
            </div>
        </div>

        <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = this.querySelector('button[type="submit"]');
            if (btn.disabled) return;
            btn.disabled = true;
            btn.textContent = 'Запазване...';

            var checked = Array.from(this.querySelectorAll('input[name="types[]"]:checked')).map(function(c) { return parseInt(c.value); });
            fetch('/clipboard/edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: parseInt(this.querySelector('input[name="id"]').value),
                    name: this.name.value,
                    description: this.description.value,
                    visibility: this.visibility.value,
                    mode: this.modeInput.value,
                    allowed_resource_types: checked
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    window.location.href = '/clipboard?id=' + document.querySelector('input[name="id"]').value;
                } else {
                    alert(d.error || 'Грешка при запазване');
                    btn.disabled = false;
                    btn.textContent = 'Запази';
                }
            })
            .catch(function() {
                alert('Грешка при свързване');
                btn.disabled = false;
                btn.textContent = 'Запази';
            });
        });
        </script>
