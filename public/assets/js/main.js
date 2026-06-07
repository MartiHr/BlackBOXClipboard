document.addEventListener('DOMContentLoaded', function() {
    var notifBell = document.getElementById('notifBell');
    var notifBadge = document.getElementById('notifBadge');
    var notifDropdown = document.getElementById('notifDropdown');
    var notifList = document.getElementById('notifList');
    var markAllReadBtn = document.getElementById('markAllReadBtn');

    if (!notifBell) return;

    function pollNotifications() {
        fetch(window.BASE_PATH + '/notifications/poll')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.unread > 0) {
                    notifBadge.textContent = d.unread;
                    notifBadge.style.display = '';
                } else {
                    notifBadge.style.display = 'none';
                }
            });
    }

    function loadNotifications() {
        fetch(window.BASE_PATH + '/notifications/list')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                notifList.innerHTML = '';
                if (!d.notifications || d.notifications.length === 0) {
                    notifList.innerHTML = '<div class="notif-empty">Няма нотификации</div>';
                    return;
                }
                d.notifications.forEach(function(n) {
                    var el = document.createElement('a');
                    el.className = 'notif-item' + (n.is_read ? '' : ' notif-unread');
                    el.href = window.BASE_PATH + '/clipboard?id=' + n.clipboard_id;
                    el.innerHTML = '<div class="notif-message">' + n.message + '</div><div class="notif-time">' + n.created_at + '</div>';
                    el.addEventListener('click', function(e) {
                        fetch(window.BASE_PATH + '/notifications/read', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: n.id })
                        });
                    });
                    notifList.appendChild(el);
                });
            });
    }

    notifBell.addEventListener('click', function(e) {
        e.stopPropagation();
        if (notifDropdown.style.display === 'block') {
            notifDropdown.style.display = 'none';
            return;
        }
        loadNotifications();
        notifDropdown.style.display = 'block';
    });

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch(window.BASE_PATH + '/notifications/read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ all: true })
            });
            notifDropdown.style.display = 'none';
            notifBadge.style.display = 'none';
        });
    }

    document.addEventListener('click', function() {
        if (notifDropdown) notifDropdown.style.display = 'none';
    });

    pollNotifications();
    setInterval(pollNotifications, 10000);
});
