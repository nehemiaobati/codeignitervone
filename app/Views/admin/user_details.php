<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 class="mt-5">User Details</h1>


    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Username: <?= esc($user->username) ?></h5>
            <p class="card-text">Email: <?= esc($user->email) ?></p>
            <p class="card-text">Current Balance: $<?= number_format($user->balance, 2) ?></p>
        </div>
    </div>

    <h2 class="mt-4">Update Balance</h2>
    <form action="<?= url_to('admin.users.update_balance', $user->id) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="amount" class="form-label">Amount:</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="col-auto">
                <label for="action" class="form-label">Action:</label>
                <select name="action" id="action" class="form-select" required>
                    <option value="deposit">Deposit</option>
                    <option value="withdraw">Withdraw</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mt-3">Update Balance</button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <a href="<?= url_to('admin.index') ?>" class="btn btn-secondary">Back to User List</a>
    </div>
</div>
<?= $this->endSection() ?>
