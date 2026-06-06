<section style="max-width: 720px; margin: 2rem auto; padding: 1rem;">
    <h1>Нова Clipboard</h1>

    <form id="clipboardCreateForm">
        <div style="margin-bottom: 1rem;">
            <label for="name">Име</label><br>
            <input id="name" name="name" type="text" required style="width:100%; padding:0.6rem;" />
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="description">Описание</label><br>
            <textarea id="description" name="description" rows="4" style="width:100%; padding:0.6rem;"></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Resource Types</label>
            <div style="display:grid; gap:0.5rem; margin-top:0.5rem;">
                <?php foreach ($resourceTypes as $type): ?>
                    <label style="border:1px solid #ccc; padding:0.6rem; border-radius:6px; display:flex; align-items:center; gap:0.5rem;">
                        <input type="checkbox" name="allowed_resource_types[]" value="<?= htmlspecialchars($type['id']) ?>" />
                        <span><?= htmlspecialchars($type['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="visibility">Visibility</label><br>
            <select id="visibility" name="visibility" style="width:100%; padding:0.6rem;">
                <option value="private">Private</option>
                <option value="public">Public</option>
            </select>
        </div>

        <button type="submit" style="padding:0.8rem 1.2rem;">Create Clipboard</button>
        <p id="statusMessage" style="margin-top:1rem;color:#333;"></p>
    </form>
</section>

<script>
    const form = document.getElementById('clipboardCreateForm');
    const statusMessage = document.getElementById('statusMessage');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const payload = {
            name: formData.get('name'),
            description: formData.get('description'),
            visibility: formData.get('visibility'),
            allowed_resource_types: formData.getAll('allowed_resource_types[]')
        };

        const response = await fetch('/clipboard/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            statusMessage.textContent = 'Clipboard created: ' + result.clipboard_id;
            statusMessage.style.color = 'green';
            form.reset();
        } else {
            statusMessage.textContent = result.error || 'Неуспех при създаване';
            statusMessage.style.color = 'red';
        }
    });
</script>
