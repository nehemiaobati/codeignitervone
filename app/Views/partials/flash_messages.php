<?php
// Check for general success message
if ($success_data = session()->getFlashdata('success')) :
    $success_message = is_array($success_data) ? implode(', ', $success_data) : $success_data;
?>
    <div class="alert alert-success" role="alert">
        <?= esc($success_message) ?>
    </div>
<?php
// Check for general error message
elseif ($error_data = session()->getFlashdata('error')) :
    $error_message = is_array($error_data) ? implode(', ', $error_data) : $error_data;
?>
    <div class="alert alert-danger" role="alert">
        <?= esc($error_message) ?>
    </div>
<?php
// Check for specific alert message
elseif ($alert_data = session()->getFlashdata('alert')) :
    $alert_message = is_array($alert_data) ? implode(', ', $alert_data) : $alert_data;
?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= esc($alert_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
// Check for warning message
elseif ($warning_data = session()->getFlashdata('warning')) :
    $warning_message = is_array($warning_data) ? implode(', ', $warning_data) : $warning_data;
?>
    <div class="alert alert-warning" role="alert">
        <?= esc($warning_message) ?>
    </div>
<?php endif; ?>
