<?php declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Nette\DI\Container;

/** @var Container $container */
$container = require __DIR__ . '/app/bootstrap.php';
/** @var Connection $connection */
$connection = $container->getByType(Connection::class);

return [
	'paths' => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/app/migrations',
	],
	'environments' => [
		'default_database' => 'default',
		'default' => [
			'name' => 'meft',
			'connection' => $connection->getWrappedConnection(),
		],
	],
];
