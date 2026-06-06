<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Канали | BlackBOX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/shared.css">
    <link rel="stylesheet" href="/assets/css/channels.css">
</head>
<body>

    <header class="main-header">
        <div class="header-container">
            <div class="brand-logo">BlackBOX</div>
            <nav class="nav-links">
                <a href="/" class="active">Channels</a>
                <a href="#">Create New</a>
            </nav>
            <div class="header-actions">
                <button class="btn btn-outline btn-icon-only" id="syncBtn"><span class="material-symbols-outlined">sync</span></button>
                <button class="btn btn-primary">Profile</button>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <section class="dashboard-header">
            <div>
                <div class="status-indicator">
                    <div class="pulse-dot"></div>
                    <span class="status-text">All systems operational</span>
                </div>
                <h1>Data Channels</h1>
                <p class="dashboard-title-desc">
                    Clinical, real-time synchronization hubs for your clipboard fragments. Monitor status and bridge data across instances.
                </p>
            </div>
            <button class="btn btn-primary btn-fit">
                <span class="material-symbols-outlined">add</span> Create New Channel
            </button>
        </section>

        <section class="bento-grid">
            <?php if (!empty($clipboards)): ?>
                <?php foreach ($clipboards as $c): ?>
                    <div class="channel-card">
                        <div class="card-top">
                            <div>
                                <span class="channel-status <?= $c['visibility'] === 'private' ? 'channel-status-standby' : '' ?>">
                                    <?= ucfirst($c['visibility']) ?> Hub
                                </span>
                                <h3><?= htmlspecialchars($c['name']) ?></h3>
                            </div>
                            <span class="material-symbols-outlined">
                                <?= $c['visibility'] === 'private' ? 'lock' : 'terminal' ?>
                            </span>
                        </div>
                        <div class="card-stats">
                            <div class="stat-row">
                                <span>Created:</span>
                                <span><?= htmlspecialchars(date('M j, Y', strtotime($c['created_at']))) ?></span>
                            </div>
                            <div class="stat-row">
                                <span>Slug:</span>
                                <span><?= htmlspecialchars($c['slug']) ?></span>
                            </div>
                            <a href="/clipboard/<?= htmlspecialchars($c['slug']) ?>" class="btn btn-outline card-action-btn">View Channel</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status">Active Hub</span>
                            <h3>Production_Main</h3>
                        </div>
                        <span class="material-symbols-outlined">terminal</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Last Update:</span><span>2 mins ago</span></div>
                        <div class="stat-row"><span>Packets:</span><span>1,248</span></div>
                        <button class="btn btn-outline card-action-btn">View Channel</button>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status channel-status-standby">Standby</span>
                            <h3>Staging_Vault</h3>
                        </div>
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Last Update:</span><span>1 hour ago</span></div>
                        <div class="stat-row"><span>Packets:</span><span>42</span></div>
                        <button class="btn btn-outline card-action-btn">View Channel</button>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="card-top">
                        <div>
                            <span class="channel-status">Auto-Sync</span>
                            <h3>Mobile_Proxy</h3>
                        </div>
                        <span class="material-symbols-outlined">smartphone</span>
                    </div>
                    <div class="card-stats">
                        <div class="stat-row"><span>Last Update:</span><span>Just Now</span></div>
                        <div class="stat-row"><span>Packets:</span><span>8,912</span></div>
                        <button class="btn btn-outline card-action-btn">View Channel</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="empty-card">
                <div class="icon-circle">
                    <span class="material-symbols-outlined">add</span>
                </div>
                <span class="empty-card-text">Initialize New Box</span>
            </div>
        </section>

        <section class="stats-section">
            <div class="stats-card">
                <div class="progress-bar-container">
                    <span class="progress-bar-label">Global Throughput</span>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                <div class="stats-value-box">
                    <span class="stats-large-num">8.4 GB</span>
                    <span class="status-text">Transferred Today</span>
                </div>
            </div>

            <div class="stats-card stats-card-vertical">
                <span class="progress-bar-label">Connected Nodes</span>
                <span class="stats-large-num"><?= count($clipboards) ?></span>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <span class="footer-copyright">© 2026 BlackBOX Clipboard.</span>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">API Documentation</a>
            </div>
        </div>
    </footer>

    <script>
        const syncBtn = document.getElementById('syncBtn');
        if (syncBtn) {
            syncBtn.addEventListener('click', function() {
                const icon = this.querySelector('.material-symbols-outlined');
                icon.style.transform = 'rotate(360deg)';
                icon.style.transition = 'transform 0.5s ease-in-out';
                setTimeout(() => {
                    icon.style.transform = 'rotate(0deg)';
                    icon.style.transition = 'none';
                }, 500);
            });
        }
    </script>
</body>
</html>
