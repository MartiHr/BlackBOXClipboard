document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sub-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var enabled = this.dataset.enabled === '1';
            var newEnabled = !enabled;
            var icon = this.querySelector('.material-symbols-outlined');

            fetch('/clipboard/subscribe/email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    clipboard_id: parseInt(this.dataset.clipboardId),
                    notify_email: newEnabled
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    btn.dataset.enabled = newEnabled ? '1' : '0';
                    icon.textContent = newEnabled ? 'mail' : 'notifications';
                }
            });
        });
    });
});
