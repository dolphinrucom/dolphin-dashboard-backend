<?php

namespace Models\Db;

use Models\Db;

class Data extends Db
{
    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    public function insert($source, $value)
    {
        $data = [
            'source'       => $source,
            'value'        => $value,
            'value_string' => (string)$value,
            'value_number' => (float)$value,
            'date'         => date('Y-m-d'),
            'datetime'     => date('Y-m-d H:i:s')
        ];
        $sql = <<<SQL
INSERT INTO data (`source`, `value`, `value_string`, `value_number`, `date`, `datetime`) 
VALUES (:source, :value, :value_string, :value_number, :date, :datetime)
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
}
