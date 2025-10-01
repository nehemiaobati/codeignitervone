<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'My Application') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Custom Hover Effects */
        .card-hoverable {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }

        .card-hoverable:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-hover-effect {
            transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        .btn-hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-input-focus:focus {
            border-color: #007bff; /* Bootstrap primary color */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand fs-4 fw-bold text-primary" href="<?= url_to('welcome') ?>">AFRIKENKID</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto text-start">
                        <?php if (session()->get('isLoggedIn')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hello, <?= esc(session()->get('username')) ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="<?= url_to('home') ?>">Home</a></li>
                                    <li><a class="dropdown-item" href="<?= url_to('payment.index') ?>">Make Payment</a></li>
                                    <li><a class="dropdown-item" href="<?= url_to('crypto.index') ?>">Crypto Service</a></li>
                                    <li><a class="dropdown-item" href="<?= url_to('gemini.index') ?>">Gemini API</a></li>
                                    <?php if (session()->get('is_admin')): ?>
                                        <li><a class="dropdown-item" href="<?= url_to('admin.index') ?>">Admin</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= url_to('logout') ?>">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="<?= url_to('login') ?>">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= url_to('register') ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-3">
        <?= $this->include('partials/flash_messages') ?>
    </div>

    <main class="flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-white text-dark text-center py-4 mt-auto border-top">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> AFRIKENKID. All rights reserved.</p>
            <p class="mb-0">
                <a href="<?= url_to('terms') ?>" class="text-dark me-3">Terms of Service</a>
                <a href="<?= url_to('privacy') ?>" class="text-dark">Privacy Policy</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
