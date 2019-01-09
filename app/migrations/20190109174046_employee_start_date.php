<?php

use Phinx\Migration\AbstractMigration;

class EmployeeStartDate extends AbstractMigration
{
	public function change()
	{
		$this->table('employee')
			->addColumn('start_date', 'date', ['null' => true])
			->update();
	}
}
