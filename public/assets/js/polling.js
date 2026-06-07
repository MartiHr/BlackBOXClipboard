document.addEventListener('DOMContentLoaded', function() {
    var shareDashboard = document.getElementById('shareDashboard');
    if (!shareDashboard) return;

    var shareCode = shareDashboard.dataset.shareCode;
    var lastId = Number(shareDashboard.dataset.lastId || 0);
    var feed = document.getElementById('shareFeed');
    var toastContainer = document.getElementById('toastContainer');
    var itemsCount = document.getElementById('itemsCount');
    var liveStatusTime = document.getElementById('liveStatusTime');
    var newItemsCount = document.getElementById('newItemsCount');
    var copyClipboardLinkBtn = document.getElementById('copyClipboardLinkBtn');

    function updateLiveMeta(newCount) {
        if (newItemsCount) {
            newItemsCount.textContent = newCount + ' нови';
            newItemsCount.style.opacity = newCount > 0 ? '1' : '0.65';
        }
        if (liveStatusTime) {
            liveStatusTime.textContent = 'последно: ' + new Date().toLocaleTimeString('bg-BG', { hour: '2-digit', minute: '2-digit' });
        }
    }

    function showToast(message) {
        if (!toastContainer) return;
        var toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.innerHTML = '<span>' + message + '</span><button type="button">OK</button>';
        toastContainer.appendChild(toast);

        toast.querySelector('button').addEventListener('click', function() {
            toast.remove();
        });

        setTimeout(function() {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 6500);
    }

    function copyText(text) {
        if (!text) return;
        navigator.clipboard?.writeText(text).then(function() {
            showToast('Копирано в клипборда!');
        }).catch(function() {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('Копирано в клипборда!');
        });
    }

    function createCardHtml(item) {
        var body = '';
        if (item.display_type === 'image') {
            body = '<img class="feed-image" src="' + item.content + '" alt="Shared image" loading="lazy">';
        } else if (item.display_type === 'file') {
            body = '<div class="feed-file-card"><span class="material-symbols-outlined">download</span><a href="' + item.content + '" target="_blank">Свали файл</a></div>';
        } else if (item.display_type === 'link') {
            body = '<a class="feed-link" href="' + item.content + '" target="_blank" rel="noopener">' + item.content + '</a>';
        } else if (item.display_type === 'code') {
            body = '<pre>' + escapeHtml(item.content) + '</pre>';
        } else {
            body = '<p>' + escapeHtml(item.content).replace(/\n/g, '<br>') + '</p>';
        }

        return '<article class="feed-card new-item" data-item-id="' + item.id + '">' +
            '<div class="feed-card-header">' +
            '<div><span class="feed-badge type-' + item.display_type + '">' + item.type_label + '</span> <span class="feed-time">' + item.time_ago + '</span></div>' +
            '<button class="btn btn-outline btn-icon copy-item-btn" data-content="' + escapeAttribute(item.content) + '">Копирай</button>' +
            '</div>' +
            '<div class="feed-card-body">' + body + '</div>' +
            '</article>';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function escapeAttribute(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function bindCopyActions(card) {
        var button = card.querySelector('.copy-item-btn');
        if (!button) return;
        button.addEventListener('click', function() {
            copyText(button.dataset.content || '');
        });
    }

    function updateFeed(items) {
        if (!Array.isArray(items) || items.length === 0) {
            updateLiveMeta(0);
            return;
        }

        var count = items.length;
        updateLiveMeta(count);

        items.reverse().forEach(function(item) {
            var html = createCardHtml(item);
            if (feed) {
                feed.insertAdjacentHTML('afterbegin', html);
                var card = feed.querySelector('.feed-card.new-item');
                if (card) {
                    window.requestAnimationFrame(function() {
                        card.classList.add('show');
                    });
                    bindCopyActions(card);
                }
            }
        });

        if (itemsCount) {
            itemsCount.textContent = Number(itemsCount.textContent || 0) + count;
        }

        showToast(count + ' нов запис получен');
    }

    function fetchUpdates() {
        if (!shareCode) return;

        fetch('/clipboard/updates?code=' + encodeURIComponent(shareCode) + '&last_id=' + encodeURIComponent(lastId))
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data || !data.success) {
                    return;
                }
                if (Array.isArray(data.items) && data.items.length > 0) {
                    updateFeed(data.items);
                }
                lastId = Number(data.last_id || lastId);
            })
            .catch(function() {
                updateLiveMeta(0);
            });
    }

    if (feed) {
        feed.querySelectorAll('.copy-item-btn').forEach(bindCopyActions);
    }

    if (copyClipboardLinkBtn) {
        copyClipboardLinkBtn.addEventListener('click', function() {
            copyText(copyClipboardLinkBtn.dataset.url || window.location.href);
        });
    }

    updateLiveMeta(0);
    fetchUpdates();
    setInterval(fetchUpdates, 3000);
});
