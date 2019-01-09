<?php

use Phinx\Migration\AbstractMigration;

class EventDepartments extends AbstractMigration
{
	public function change()
	{
		$this->table('event_department', ['id' => false, 'primary_key' => ['event_id', 'department_id']])
			->addColumn('event_id', 'integer')
			->addForeignKey('event_id', 'event', 'id', ['delete' => 'CASCADE'])
			->addColumn('department_id', 'integer')
			->addForeignKey('department_id', 'department', 'id', ['delete' => 'CASCADE'])
			->create();
	}
}
