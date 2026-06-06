        <div class="auth-card auth-card-wide">
            <div class="card-header">
                <h1>Създайте акаунт</h1>
                <p>Присъединете се към BlackBOX за безпроблемен универсален клипборд.</p>
            </div>

            <form method="POST">
                <?php if (!empty($message)): ?>
                    <div class="form-message"><?= $message ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Пълно име</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Иван Иванов" required>
                </div>

                <div class="form-group">
                    <label for="email">Имейл</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="ime@company.com" required>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="password">Парола</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Потвърдете паролата</label>
                        <input type="password" id="confirm-password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Регистрация</button>
            </form>

            <div class="form-footer-redirect">
                <p>Вече имате акаунт? <a href="/login">Влезте</a></p>
            </div>

            <div class="encryption-badge">
                <span class="material-symbols-outlined">lock</span>
                <span>Криптиране тип край-до-край</span>
            </div>
        </div>
