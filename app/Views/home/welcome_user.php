<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h3 class="mb-0">User Dashboard</h3>
                </div>
                <div class="card-body p-5">
                    <h4 class="card-title text-center mb-4">Welcome, <?= esc($username ?? 'User') ?>!</h4>
                    
                    <div class="card my-4 text-start shadow-sm">
                        <div class="card-header">
                           <h5 class="mb-0 text-primary">Account Information</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <strong>Username:</strong>
                                    <span class="text-muted"><?= esc($username ?? 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <strong>Email:</strong>
                                    <span class="text-muted"><?= esc($email ?? 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <strong>Member Since:</strong>
                                    <span class="text-muted"><?= esc($member_since ? date('F d, Y', strtotime($member_since)) : 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <strong>Current Balance:</strong>
                                    <span class="fw-bold fs-5 text-success">$<?= esc(number_format((float)($balance ?? 0), 2)) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <strong>Service Status:</strong>
                                    <?php if (isset($balance) && (float)$balance > 0): ?>
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger rounded-pill">Payment Required</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">To use the Crypto Service, you must have an active balance. Add funds to your account now.</p>
                        <a href="<?= url_to('payment.index') ?>" class="btn btn-primary btn-lg mt-2 px-5 btn-hover-effect">
                            Make a Payment
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    Need help? <a href="<?= url_to('contact.form') ?>">Contact Support</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
