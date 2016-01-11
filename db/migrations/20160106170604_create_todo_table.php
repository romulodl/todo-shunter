<?php

use Phinx\Migration\AbstractMigration;

class CreateTodoTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('item');
        $table->addColumn('title', 'string')
              ->addColumn('done', 'boolean')
              ->create();
    }
}
