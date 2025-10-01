<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-lg text-center">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Contact Us</h3>
                </div>
                <div class="card-body p-5">
                    <p class="card-text text-muted mb-4">Have questions or need assistance? Fill out the form below and we'll get back to you as soon as possible.</p>

                    <?= form_open(url_to('contact.send'), ['class' => 'text-start']) ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg btn-hover-effect">Send Message</button>
                        </div>
                    <?= form_close() ?>
                </div>
                <div class="card-footer text-muted">
                    Return to <a href="<?= url_to('home') ?>">Dashboard</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
