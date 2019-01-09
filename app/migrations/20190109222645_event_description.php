<?php

use Phinx\Migration\AbstractMigration;

class EventDescription extends AbstractMigration
{
	public function change()
	{
		$this->table('event')
			->addColumn('description', 'text')
			->update();
	}
}
