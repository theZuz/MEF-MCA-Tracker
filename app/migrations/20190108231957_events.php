<?php

use Phinx\Migration\AbstractMigration;

class Events extends AbstractMigration
{
	public function change()
	{
		$this->table('event')
			->addColumn('manager_id', 'integer')
			->addForeignKey('manager_id', 'manager', 'id', ['delete' => 'CASCADE'])
			->addColumn('name', 'string')
			->addColumn('date', 'date')
			->addColumn('price', 'float')
			->create();

		$this->table('event_employee', ['id' => false, 'primary_key' => ['event_id', 'employee_id']])
			->addColumn('event_id', 'integer')
			->addForeignKey('event_id', 'event', 'id', ['delete' => 'CASCADE'])
			->addColumn('employee_id', 'integer')
			->addForeignKey('employee_id', 'employee', 'id', ['delete' => 'CASCADE'])
			->create();
	}
}
