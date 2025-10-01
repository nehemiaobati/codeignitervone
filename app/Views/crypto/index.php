<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?></h3>
                </div>
                <div class="card-body">
                    <form action="<?= url_to('crypto.query') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="asset" class="form-label">Cryptocurrency Asset</label>
                            <select class="form-control form-input-focus" id="asset" name="asset" required>
                                <option value="">Select Asset</option>
                                <option value="btc" <?= old('asset') == 'btc' ? 'selected' : '' ?>>Bitcoin (BTC)</option>
                                <option value="ltc" <?= old('asset') == 'ltc' ? 'selected' : '' ?>>Litecoin (LTC)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="query_type" class="form-label">Query Type</label>
                            <select class="form-control form-input-focus" id="query_type" name="query_type" required>
                                <option value="">Select Query Type</option>
                                <option value="balance" <?= old('query_type') == 'balance' ? 'selected' : '' ?>>Balance</option>
                                <option value="tx" <?= old('query_type') == 'tx' ? 'selected' : '' ?>>Transactions</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Wallet Address</label>
                            <input type="text" class="form-control form-input-focus" id="address" name="address" value="<?= old('address') ?>" required>
                        </div>
                        <div class="mb-3" id="limit-field" style="display: <?= old('query_type') == 'tx' ? 'block' : 'none' ?>;">
                            <label for="limit" class="form-label">Number of Transactions (for 'Transactions' query)</label>
                            <input type="number" class="form-control" id="limit" name="limit" value="<?= old('limit') ?>" min="1" max="50">
                        </div>
                        <button type="submit" class="btn btn-primary btn-hover-effect">Query Crypto</button>
                    </form>

                    <?php if (session()->getFlashdata('result')): ?>
                        <div class="mt-4 p-3 border rounded bg-light">
                            <h4 class="mb-3">Query Result:</h4>
                            <?php $result = session()->getFlashdata('result'); ?>
                            <p><strong>Asset:</strong> <?= esc($result['asset'] ?? 'N/A') ?></p>
                            <p><strong>Address:</strong> <?= esc($result['address'] ?? 'N/A') ?></p>
                            <p><strong>Query:</strong> <?= esc($result['query'] ?? 'N/A') ?></p>

                            <?php if (isset($result['balance'])): ?>
                                <p><strong>Balance:</strong> <?= esc($result['balance']) ?></p>
                            <?php elseif (isset($result['transactions'])): ?>
                                <h5 class="mb-3">Transactions:</h5>
                                <?php if (!empty($result['transactions'])): ?>
                                    <div class="accordion" id="transactionsAccordion">
                                        <?php foreach ($result['transactions'] as $index => $tx): ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading<?= $index ?>">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                                                        Transaction <?= $index + 1 ?>: <?= esc($tx['hash']) ?>
                                                    </button>
                                                </h2>
                                                <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#transactionsAccordion">
                                                    <div class="accordion-body">
                                                        <p><strong>Hash:</strong> <?= esc($tx['hash']) ?></p>
                                                        <p><strong>Time:</strong> <?= esc($tx['time'] ?? 'N/A') ?></p>
                                                        <p><strong>Block:</strong> <?= esc($tx['block_height'] ?? $tx['block_id'] ?? 'N/A') ?></p>
                                                        <p><strong>Fee:</strong> <?= esc($tx['fee'] ?? 'N/A') ?></p>

                                                        <h6>Sending Addresses:</h6>
                                                        <?php if (!empty($tx['sending_addresses'])): ?>
                                                            <ul class="list-group mb-2">
                                                                <?php foreach ($tx['sending_addresses'] as $s_addr): ?>
                                                                    <li class="list-group-item list-group-item-sm"><?= esc($s_addr) ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p>No sending addresses found.</p>
                                                        <?php endif; ?>

                                                        <h6>Receiving Addresses:</h6>
                                                        <?php if (!empty($tx['receiving_addresses'])): ?>
                                                            <ul class="list-group">
                                                                <?php foreach ($tx['receiving_addresses'] as $r_addr): ?>
                                                                    <li class="list-group-item list-group-item-sm">
                                                                        <?= esc($r_addr['address']) ?> (Amount: <?= esc($r_addr['amount']) ?>)
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p>No receiving addresses found.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No transactions found.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const queryTypeSelect = document.getElementById('query_type');
        const limitField = document.getElementById('limit-field');

        function toggleLimitField() {
            if (queryTypeSelect.value === 'tx') {
                limitField.style.display = 'block';
            } else {
                limitField.style.display = 'none';
            }
        }

        queryTypeSelect.addEventListener('change', toggleLimitField);

        // Initial call to set the correct state based on old input
        toggleLimitField();
    });
</script>
<?= $this->endSection() ?>
