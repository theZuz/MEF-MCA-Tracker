<?php

use Phinx\Migration\AbstractMigration;

class Managers extends AbstractMigration
{
	public function change()
	{
		$this->table('manager')
			->addColumn('email', 'string')
			->addIndex('email', ['unique' => true])
			->addColumn('password', 'string')
			->create();
	}
}
