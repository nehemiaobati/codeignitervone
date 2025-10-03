<?php declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = \App\Entities\User::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'password', 'username', 'balance', 'verification_token', 'is_verified'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getTotalBalance(): string
    {
        $totalBalanceData = $this->selectSum('balance')->first();
        return $totalBalanceData ? $totalBalanceData->balance : '0.00';
    }

    public function deductBalance(int $userId, int $amount): bool
    {
        $user = $this->find($userId);

        if ($user && $user->balance >= $amount) {
            $user->balance -= $amount;
            return $this->save($user);
        }

        return false;
    }

    public function addBalance(int $userId, string $amount): bool
    {
        $user = $this->find($userId);

        if ($user) {
            $currentBalance = is_string($user->balance) ? $user->balance : (string) $user->balance;
            $user->balance = bcadd($currentBalance ?? '0.00', $amount, 2);
            return $this->save($user);
        }

        return false;
    }
}
