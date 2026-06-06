        <div class="auth-card">
            <div class="card-header">
                <h1>Добре дошли</h1>
                <p>Достъпете своя универсален клипборд от всяко устройство.</p>
            </div>

            <form id="loginForm" method="POST">
                <?php if (!empty($error)): ?>
                    <div class="form-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="identifier">Потребителско име / Имейл</label>
                    <input type="text" id="identifier" name="username" class="form-control mono-input" placeholder="напр. dev_user_01" required>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Парола</label>
                        <a href="#">Забравена?</a>
                    </div>
                    <input type="password" id="password" name="password" class="form-control mono-input" required>
                </div>

                <button type="submit" class="btn btn-primary">Вход</button>
            </form>

            <div class="form-footer-redirect">
                <p>Нямате акаунт? <a href="/register">Регистрирайте се</a></p>
            </div>

            <div class="encryption-badge">
                <span class="material-symbols-outlined">lock</span>
                <span>Криптиране тип край-до-край</span>
            </div>
        </div>
