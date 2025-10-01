<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5 shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($validation)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $validation->listErrors() ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?= url_to('login.authenticate') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control form-input-focus" id="email" name="email" value="<?= esc(old('email')) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-input-focus" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-hover-effect">Login</button>
                            <p class="mt-3 text-center">Don't have an account? <a href="<?= url_to('register') ?>">Register here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
