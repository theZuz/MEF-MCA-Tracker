<?php

use Phinx\Migration\AbstractMigration;

class Departments extends AbstractMigration
{
	public function change()
	{
		$this->table('department')
			->addColumn('manager_id', 'integer')
			->addForeignKey('manager_id', 'manager', 'id', ['delete' => 'CASCADE'])
			->addColumn('code', 'string')
			->addIndex('code', ['unique' => true])
			->create();
	}
}
