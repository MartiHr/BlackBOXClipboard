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

    var selectAll = document.getElementById('selectAllTypes');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            var checked = this.checked;
            document.querySelectorAll('.resource-grid input[name="types[]"]').forEach(function(cb) {
                cb.checked = checked;
            });
        });
    }

    var form = document.getElementById('createForm');
    var submitBtn = form.querySelector('button[type="submit"]');
    var toggleTypesCheckbox = document.getElementById('toggleResourceTypes');
    var toggleTypesLabel = document.getElementById('toggleResourceTypesLabel');

    function updateToggleLabel(allChecked) {
        toggleTypesLabel.textContent = allChecked ? 'Премахни всички' : 'Избери всички';
    }

    function syncToggleLabel() {
        var checkboxes = Array.from(document.querySelectorAll('input[name="types[]"]'));
        var allChecked = checkboxes.length > 0 && checkboxes.every(function(checkbox) { return checkbox.checked; });
        toggleTypesCheckbox.checked = allChecked;
        updateToggleLabel(allChecked);
    }

    toggleTypesCheckbox.addEventListener('change', function() {
        var checkboxes = Array.from(document.querySelectorAll('input[name="types[]"]'));
        checkboxes.forEach(function(checkbox) { checkbox.checked = toggleTypesCheckbox.checked; });
        updateToggleLabel(toggleTypesCheckbox.checked);
    });

    document.querySelectorAll('input[name="types[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', syncToggleLabel);
    });

    syncToggleLabel();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Създаване...';

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
            if (d.success) {
                window.location.href = '/';
            } else {
                alert(d.error || 'Грешка при създаване');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Създай клипборд';
            }
        }).catch(function() {
            alert('Грешка при свързване със сървъра');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Създай клипборд';
        });
    });
});
