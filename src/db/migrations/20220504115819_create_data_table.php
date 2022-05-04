<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDataTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('data');
        $table->addColumn('source', 'string')
            ->addColumn('value', 'string')
            ->addColumn('value_string', 'string')
            ->addColumn('value_number', 'float')
            ->addColumn('date', 'date')
            ->addColumn('datetime', 'datetime')
            ->create();
    }
}
