<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    .query-card, .results-card {
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        border: none;
    }
    .results-card pre {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .file-input-group {
        display: flex;
        gap: 0.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">
            <div class="card query-card">
                 <div class="card-body p-4 p-md-5">
                    <h2 class="card-title fw-bold mb-4">Gemini AI Service</h2>
                    <form action="<?= url_to('gemini.generate') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="form-floating mb-3">
                            <textarea id="prompt" name="prompt" class="form-control" placeholder="Enter your prompt" style="height: 150px" required><?= old('prompt') ?></textarea>
                            <label for="prompt">Your Prompt</label>
                        </div>

                        <div id="mediaInputContainer" class="mb-2">
                             <div class="mb-3 media-input-row">
                                <label for="media" class="form-label">Upload Media (Optional)</label>
                                <div class="file-input-group">
                                    <input type="file" class="form-control" name="media[]" multiple>
                                    <button type="button" class="btn btn-outline-danger remove-media-btn" style="display: none;">Remove</button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="addMediaBtn" class="btn btn-secondary btn-sm mb-4"><i class="bi bi-plus-circle"></i> Add Another File</button>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="bi bi-robot"></i> Generate Content</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($result = session()->getFlashdata('result')): ?>
        <div class="col-lg-8">
            <div class="card results-card mt-4">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-4">AI Response</h3>
                    <pre><?= esc($result) ?></pre>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addBtn = document.getElementById('addMediaBtn');
        const container = document.getElementById('mediaInputContainer');

        // Function to add a new file input row
        function addMediaInputRow() {
            const newRow = document.createElement('div');
            newRow.className = 'mb-3 media-input-row';
            newRow.innerHTML = `
                <div class="file-input-group">
                    <input type="file" class="form-control" name="media[]" multiple>
                    <button type="button" class="btn btn-outline-danger remove-media-btn">Remove</button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Show remove button on the first input if it's hidden
            const firstRemoveBtn = container.querySelector('.media-input-row:first-child .remove-media-btn');
            if(firstRemoveBtn) {
                firstRemoveBtn.style.display = 'inline-block';
            }
        }

        // Add button event listener
        addBtn.addEventListener('click', addMediaInputRow);

        // Event delegation for remove buttons
        container.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-media-btn')) {
                // Remove the entire parent row
                event.target.closest('.media-input-row').remove();
                
                // If only one input row is left, hide its remove button
                const remainingRows = container.querySelectorAll('.media-input-row');
                if (remainingRows.length === 1) {
                    const lastRemoveBtn = remainingRows[0].querySelector('.remove-media-btn');
                    if(lastRemoveBtn) {
                        lastRemoveBtn.style.display = 'none';
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>