<?php

return [

	'command_namespace' => 'Goose\\App\\Command',
	'handler_namespace' => 'Goose\\App\\Handler',
	'command_interface_namespace' => 'Goose\\App\\Command\\CommandInterface',
	'handler_interface_namespace' => 'Goose\\App\\Handler\\HandlerInterface',
	'output' => [
		'command' => __DIR__ . '/output/commands/',
		'handler' => __DIR__ . '/output/handlers/',
	],

	// This dependencies should be the aliases used in your PSR container
	'dependencies' => [
		'guzzle' => 'GuzzleHttp\\Client',
		'database' => 'mef\\Sql\\Driver\\SqlDriver',
		'jwt' => 'Firebase\\JWT\\JWT',
	],
];