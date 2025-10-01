<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>AI Gemini API Interaction</h1>
        </div>
        <div class="card-body">
            <form action="<?= url_to('gemini.generate') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="prompt" class="form-label">Enter your prompt:</label>
                    <textarea id="prompt" name="prompt" class="form-control" rows="5" required><?= old('prompt') ?></textarea>
                </div>

                <div class="mb-3 media-input-row">
                    <label for="media" class="form-label">Upload Media (Optional, multiple files allowed):</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="media[]" multiple>
                        <button type="button" class="btn btn-outline-danger remove-media-btn">Remove</button>
                    </div>
                </div>
                <div id="mediaInputContainer"></div>
                <button type="button" id="addMediaBtn" class="btn btn-secondary mb-3">Add Another Media File</button>

                <button type="submit" class="btn btn-primary w-100">Generate Content</button>
            </form>

            <?php if (isset($result) && !empty($result)): ?>
                <div class="mt-4">
                    <h4>AI Response:</h4>
                    <div class="p-3 bg-light border rounded">
                        <pre style="white-space: pre-wrap; word-wrap: break-word;"><?= esc($result) ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Function to add a new media input row
    function addMediaInputRow() {
        const mediaInputContainer = document.getElementById('mediaInputContainer');
        const newRow = document.createElement('div');
        newRow.classList.add('mb-3', 'media-input-row', 'input-group'); // Add classes for styling and grouping

        newRow.innerHTML = `
            <input type="file" class="form-control" name="media[]" multiple>
            <button type="button" class="btn btn-outline-danger remove-media-btn">Remove</button>
        `;
        mediaInputContainer.appendChild(newRow);
    }

    // Function to remove a media input row
    function removeMediaInputRow(button) {
        const rowToRemove = button.closest('.media-input-row');
        if (rowToRemove) {
            rowToRemove.remove();
        }
    }

    // Event listener for the "Add Another Media File" button
    document.getElementById('addMediaBtn').addEventListener('click', addMediaInputRow);

    // Event listener for dynamically added "Remove" buttons (using event delegation)
    document.getElementById('mediaInputContainer').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-media-btn')) {
            removeMediaInputRow(event.target);
        }
    });

    // Add event listener for the initial "Remove" button
    const initialRemoveButton = document.querySelector('.media-input-row .remove-media-btn');
    if (initialRemoveButton) {
        initialRemoveButton.addEventListener('click', function(event) {
            removeMediaInputRow(event.target);
        });
    }

</script>
<?= $this->endSection() ?>
