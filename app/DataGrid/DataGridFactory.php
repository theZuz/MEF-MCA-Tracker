<?php declare(strict_types=1);

namespace App\DataGrid;

use Ublaboo\DataGrid\DataGrid;

final class DataGridFactory
{
	public function create(): DataGrid
	{
		$grid = new DataGrid;
		$grid->setRememberState(false);

		return $grid;
	}
}
