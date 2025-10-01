<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5">

    <!-- Hero Section -->
    <section class="hero text-center mb-5 p-5 bg-white rounded-3 shadow-sm">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-3"><?= esc($heroTitle ?? 'Real-Time Crypto Data Service') ?></h1>
                <p class="lead text-muted mb-4"><?= esc($heroSubtitle ?? 'Get instant access to cryptocurrency balance and transaction data with our reliable and secure service.') ?></p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= url_to('register') ?>" class="btn btn-primary btn-lg px-4 gap-3 btn-hover-effect">Get Started</a>
                    <a href="#features" class="btn btn-outline-secondary btn-lg px-4 btn-hover-effect">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 text-center">
        <h2 class="display-5 fw-bold mb-5 text-primary">Our Core Offering</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Real-Time Balance Checks</h3>
                        <p class="text-muted">Instantly verify the balance of any public Bitcoin or Litecoin address. Our service provides up-to-the-minute data directly from the blockchain.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-card-list"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Transaction History</h3>
                        <p class="text-muted">Retrieve detailed transaction histories for any address. Understand the flow of funds with clear, concise, and accurate data.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-robot"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Gemini API Integration</h3>
                        <p class="text-muted">Leverage the power of Google's Gemini API. Interact with a cutting-edge AI to get insights, generate content, and more, directly within our platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section id="cta" class="py-5 my-5 bg-dark text-white text-center rounded-3 shadow">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-3">Ready to Access Crypto Data?</h2>
                <p class="lead mb-4">Create an account to get started. Top up your balance and gain immediate access to our powerful crypto data service.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= url_to('register') ?>" class="btn btn-primary btn-lg px-4 fw-bold btn-hover-effect">Register Now</a>
                    <a href="<?= url_to('login') ?>" class="btn btn-outline-light btn-lg px-4 btn-hover-effect">Login</a>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
