<?php

namespace Models\Db;

use Models\Db;
use PDO;

class DolphinBackOffice extends Db
{
    public function __construct()
    {
        $this->pdo = $this->getConnectionToDolphinBackOffice();
    }

    public function revenueByIp(string $ip): int
    {
        $license = $this->licenseByIp($ip);
        if (!$license) {
            return 0;
        }

        $user = $this->userById($license->user_id);
        $revenue = $this->revenueByUserId($user->id);

        return $revenue;
    }

    public function revenueByEmail(string $email): int
    {
        $user = $this->userByEmail($email);
        $revenue = $this->revenueByUserId($user->id);

        return $revenue;
    }

    public function revenueByLic(string $lic): int
    {
        $license = $this->licenseByLic($lic);
        if (!$license) {
            return 0;
        }

        $user = $this->userById($license->user_id);
        $revenue = $this->revenueByUserId($user->id);

        return $revenue;
    }

    public function licenseByIp(string $ip): object
    {
        $stmt = $this->pdo->prepare('SELECT * FROM licenses WHERE ip = :ip;');
        $stmt->execute(['ip' => $ip]);

        return (object)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function licenseByLic(string $lic): object
    {
        $stmt = $this->pdo->prepare('SELECT * FROM licenses WHERE lic = :lic;');
        $stmt->execute(['lic' => $lic]);

        return (object)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userById(int $id): object
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id;');
        $stmt->execute(['id' => $id]);

        return (object)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userByEmail(int $email): object
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email;');
        $stmt->execute(['email' => $email]);

        return (object)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function revenueByUserId(int $user_id): int
    {
        $stmt = $this->pdo->prepare('SELECT SUM(`sum`) as "sum" FROM transactions WHERE user_id = :user_id;');
        $stmt->execute(['user_id' => $user_id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return round($data['sum'] ?? 0);
    }
}
