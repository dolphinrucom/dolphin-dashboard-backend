<?php

namespace Models\Db;

use PDO;

class Slr extends \Models\Db
{
    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    public function insert(string $project, string $issue, string $date, int $seconds, int $hours)
    {
        $data = [
            'project'      => $project,
            'issue'        => $issue,
            'date_created' => $date,
            'updated_at'   => date('Y-m-d H:i:s'),
            'seconds'      => (int)$seconds,
            'hours'        => (int)$hours
        ];
        $sql = <<<SQL
DELETE FROM slr WHERE project = :project AND issue = :issue;
INSERT INTO `slr` (`project`, `issue`, `seconds`, `hours`, `date_created`, `updated_at`) 
VALUES (:project, :issue, :seconds, :hours, :date_created, :updated_at)
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function perProject(string $project)
    {
        $sql = <<<SQL
SELECT * FROM slr WHERE project = :project
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['project' => $project]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
