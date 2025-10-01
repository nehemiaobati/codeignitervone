<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 class="mt-5">Admin Dashboard</h1>
    <p>Welcome to the admin dashboard. Only administrators can see this page.</p>

    <h2 class="mt-4">User Management</h2>
    <p>Total balance of all users: $<?= number_format($total_balance, 2) ?></p>

    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc($user->username) ?></td>
                        <td><?= esc($user->email) ?></td>
                        <td>$<?= number_format($user->balance, 2) ?></td>
                        <td>
                            <a href="<?= url_to('admin.users.show', $user->id) ?>" class="btn btn-sm btn-primary">Details</a>
                            <form action="<?= url_to('admin.users.delete', $user->id) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add the pagination links -->
    <div class="d-flex justify-content-center">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
