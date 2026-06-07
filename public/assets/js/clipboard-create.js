document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mode-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.mode-option').forEach(function(b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
            document.getElementById('modeInput').value = this.dataset.value;
        });
    });

    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var checked = Array.from(this.querySelectorAll('input[name="types[]"]:checked')).map(function(c) { return parseInt(c.value); });
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
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) window.location.href = '/';
            else alert(d.error || 'Грешка при създаване');
        });
    });
});
