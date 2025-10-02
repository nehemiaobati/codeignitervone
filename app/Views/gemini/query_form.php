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
                                </div>
                            </div>
                        </div>
                        
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