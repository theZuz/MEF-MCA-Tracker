<?php

use Phinx\Migration\AbstractMigration;

class Employees extends AbstractMigration
{
	public function change()
	{
		$this->table('employee')
			->addColumn('department_id', 'integer')
			->addForeignKey('department_id', 'department', 'id', ['delete' => 'CASCADE'])
			->addColumn('cnum', 'string')
			->addColumn('name', 'string')
			->addColumn('country', 'string')
			->create();
	}
}
