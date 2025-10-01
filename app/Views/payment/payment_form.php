<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 150px);"> <!-- Adjust min-height based on header/footer height -->
        <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
            <h1 class="card-title text-center text-primary mb-4">Make a Payment</h1>

        <?= form_open(url_to('payment.initiate')) ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control form-input-focus" id="email" name="email" value="<?= esc(old('email', $email)) ?>" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount (KES)</label>
                <input type="number" class="form-control form-input-focus" id="amount" name="amount" value="<?= esc(old('amount')) ?>" min="100" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3 btn-hover-effect">Pay Now</button>
        <?= form_close() ?>
        </div>
    </div>
<?= $this->endSection() ?>
