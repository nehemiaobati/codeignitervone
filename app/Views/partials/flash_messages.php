<?php
// Check for general success message
if ($success = session()->getFlashdata('success')) : ?>
    <div class="alert alert-success" role="alert">
        <?= esc($success) ?>
    </div>
<?php
// Check for general error message
elseif ($error = session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger" role="alert">
        <?= esc($error) ?>
    </div>
<?php
// Check for specific alert message
elseif ($alert = session()->getFlashdata('alert')) : ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= esc($alert) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
// Check for validation errors
elseif ($errors = session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger" role="alert">
        <p>Please correct the following errors:</p>
        <ul class="list-unstyled mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
// Check for warning message
elseif ($warning = session()->getFlashdata('warning')) : ?>
    <div class="alert alert-warning" role="alert">
        <?= esc($warning) ?>
    </div>
<?php
// Check for validation errors
elseif ($errors = session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger" role="alert">
        <p>Please correct the following errors:</p>
        <ul class="list-unstyled mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
