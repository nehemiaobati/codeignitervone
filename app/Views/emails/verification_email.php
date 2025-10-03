<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
    <h1>Verify Your Email Address</h1>
    <p>Dear <?= esc($name) ?>,</p>
    <p>Please click the link below to verify your email address and activate your account:</p>
    <p>
        <a href="<?= $verificationLink ?>" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            Verify Email
        </a>
    </p>
    <p>If the button above does not work, copy and paste this link into your browser:</p>
    <p><?= $verificationLink ?></p>
    <p>Thank you!</p>
</body>
</html>
