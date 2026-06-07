        <div class="admin-panel">
            <div class="breadcrumb">
                <a href="<?= BASE_PATH ?>/">Клипборди</a>
                <span class="material-symbols-outlined breadcrumb-icon">chevron_right</span>
                <span class="breadcrumb-current">Админ панел</span>
            </div>

            <h1>Админ панел</h1>

            <div class="admin-cards">
                <div class="admin-card">
                    <span class="material-symbols-outlined admin-card-icon">file_download</span>
                    <h2>Export JSON</h2>
                    <p>Изтегли текущата конфигурация на клипбордовете като JSON файл.</p>
                    <a href="<?= BASE_PATH ?>/admin/export" class="btn btn-primary btn-fit">Изтегли</a>
                </div>

                <div class="admin-card">
                    <span class="material-symbols-outlined admin-card-icon">file_upload</span>
                    <h2>Import JSON</h2>
                    <p>Качи JSON файл, за да презапишеш конфигурацията на клипбордовете.</p>
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="config_file" accept=".json" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-fit">Качи</button>
                    </form>
                    <div id="importMsg" style="margin-top:8px"></div>
                </div>
            </div>
        </div>

        <script>
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = this.querySelector('button[type="submit"]');
            var msg = document.getElementById('importMsg');
            var fd = new FormData(this);
            btn.disabled = true;
            btn.textContent = 'Качване...';
            msg.textContent = '';

            fetch(window.BASE_PATH + '/admin/import', {
                method: 'POST',
                body: fd
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    msg.innerHTML = '<p style="color:green">Конфигурацията е импортирана успешно!</p>';
                } else {
                    msg.innerHTML = '<p style="color:red">' + (d.error || 'Грешка при импорт') + '</p>';
                    btn.disabled = false;
                    btn.textContent = 'Качи';
                }
            })
            .catch(function() {
                msg.innerHTML = '<p style="color:red">Грешка при свързване</p>';
                btn.disabled = false;
                btn.textContent = 'Качи';
            });
        });
        </script>
