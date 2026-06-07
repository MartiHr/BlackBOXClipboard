document.addEventListener('DOMContentLoaded', function() {
    var subBtn = document.querySelector('.subscribe-btn');
    if (!subBtn) return;

    var emailToggle = document.querySelector('.email-toggle');
    var emailLabel = document.querySelector('.switch-toggle');
    var clipboardId = subBtn.dataset.clipboardId;

    function updateEmailPref(enabled) {
        fetch('/clipboard/subscribe/email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                clipboard_id: parseInt(clipboardId),
                notify_email: enabled
            })
        });
    }

    if (emailToggle) {
        emailToggle.addEventListener('change', function() {
            updateEmailPref(this.checked);
        });
    }

    subBtn.addEventListener('click', function() {
        var isSubscribed = this.classList.contains('btn-primary');
        var self = this;

        fetch('/clipboard/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                clipboard_id: parseInt(clipboardId),
                subscribe: !isSubscribed,
                notify_email: emailToggle ? emailToggle.checked : true
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.success) return;
            if (d.subscribed) {
                self.classList.remove('btn-outline');
                self.classList.add('btn-primary');
                self.querySelector('.subscribe-label').textContent = 'Абониран';
                self.querySelector('.material-symbols-outlined').textContent = 'notifications_active';
                if (emailLabel) emailLabel.style.display = 'inline-flex';
                if (emailToggle) emailToggle.checked = d.notify_email;
            } else {
                self.classList.remove('btn-primary');
                self.classList.add('btn-outline');
                self.querySelector('.subscribe-label').textContent = 'Абонирай се';
                self.querySelector('.material-symbols-outlined').textContent = 'notifications';
                if (emailLabel) emailLabel.style.display = 'none';
            }
        });
    });
});
