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

                <div class="mb-3">
                    <label for="media" class="form-label">Upload Media (Optional, multiple files allowed):</label>
                    <input type="file" class="form-control" name="media[]" multiple>
                </div>

                <button type="submit" class="btn btn-primary">Generate Content</button>
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
<?= $this->endSection() ?>
