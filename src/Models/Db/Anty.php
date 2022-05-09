<?php

namespace Models\Db;

use PDO;

class Anty extends \Models\Db
{
    public function __construct()
    {
        $this->pdo = $this->getConnectionToAnty();
    }

    public function refRegsByDay()
    {
        $stmt = $this->pdo->prepare(
            'select DATE(created_at) as "date", count(*) from teams where refUserId != 0 group by date(created_at);'
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
