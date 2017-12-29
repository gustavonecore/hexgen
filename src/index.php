<?php require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/local.php';

$pathCommand = $config['output']['command'];
$pathHandler = $config['output']['handler'];

$twig = new Twig_Environment(new Twig_Loader_Filesystem(__DIR__ . '/../templates'));
$args = [
	'command_namespace:',
	'handler_namespace:',
	'name:',
	'command_interface_namespace:',
	'handler_interface_namespace:',
	'description::',
	'arguments::',
	'services::',
	'crud::',
];

$opts = getopt('', $args);

try
{
	// Sanity check
	foreach ($args as $arg)
	{
		$cleanArg = str_replace(':', '', $arg);

		if (substr_count($arg, ':') === 1 && !isset($config[$cleanArg]))
		{
			if (strpos($arg, ':') > 0 && (!isset($opts[$cleanArg]) || $opts[$cleanArg] === '' || $opts[$cleanArg] === null ) )
			{
				throw new RuntimeException("You must provide --$cleanArg");
			}
		}

		// Override local config
		if (isset($opts[$cleanArg]))
		{
			$config[$cleanArg] = $opts[$cleanArg];
		}
	}

	$names = explode("\\", $config['name']);
	$config['name'] = ucfirst($names[count($names) - 1]);
	$namespace = '';

	// The user want to create namespaces
	if (count($names) > 1)
	{
		$namespaces = [];

		for ($i = 0; $i < count($names) - 1; $i++)
		{
			$name = ucfirst($names[$i]);

			$pathCommand .= "/" . $name;
			$pathHandler .= "/" . $name;

			if (!file_exists($pathHandler))
			{
				mkdir($pathHandler);
			}

			if (!file_exists($pathCommand))
			{
				mkdir($pathCommand);
			}

			$namespaces[] = $name;
		}

		$namespace = "\\" . implode("\\", $namespaces);
		$pathCommand .= "/";
		$pathHandler .= "/";
	}

	$config['arguments'] = isset($opts['arguments']) ? explode(',', $opts['arguments']) : [];
	$opts['services'] = isset($opts['services']) ? explode(',', $opts['services']) : [];

	$interfaceCommandName = explode("\\", $config['command_interface_namespace']);
	$interfaceCommandName = $interfaceCommandName[count($interfaceCommandName) - 1];

	$interfaceHandlerName = explode("\\", $config['handler_interface_namespace']);
	$interfaceHandlerName = $interfaceHandlerName[count($interfaceHandlerName) - 1];

	// Write command
	file_put_contents($pathCommand . $config['name'] . 'Command.php', $twig->render('command.twig', [
		'use_interface' => 'use ' . $config['command_interface_namespace'] . ';',
		'command_interface' => $interfaceCommandName,
		'name' => $config['name'],
		'command_namespace' => $config['command_namespace'] . $namespace,
		'description' => isset($config['description']) ? $config['description'] : 'Command for ' . $config['name'],
		'arguments' => $config['arguments'],
		'arguments_constructor' => 'string $' . implode(', string $', $config['arguments']),
	]));

	$config['vendors_constructor'] = '';
	$config['vendors'] = [];

	foreach ($opts['services'] as $service)
	{
		if (isset($config['dependencies'][$service]))
		{
			$nm = $config['dependencies'][$service];
			$serviceName = explode("\\", $nm);
			$serviceName = $serviceName[count($serviceName) - 1];

			$config['vendors'][] = [
				'namespace' => $nm,
				'name' => $serviceName,
				'attr' => $service,
			];
		}
	}

	$config['vendors_constructor'] = implode(', ', array_map(function($vendor)
	{
		return $vendor['name'] . ' $' . $vendor['attr'];
	}, $config['vendors']));

	// Write handler
	file_put_contents($pathHandler . $config['name'] . 'Handler.php', $twig->render('handler.twig', [
		'use_interface' => 'use ' . $config['handler_interface_namespace'] . ';',
		'handler_interface' => $interfaceHandlerName,
		'handler_namespace' => $config['handler_namespace'] . $namespace,
		'name' => $config['name'],
		'command_namespace' => $config['command_namespace'] . $namespace,
		'description' => isset($config['description']) ? $config['description'] : 'Command for ' . $config['name'],
		'vendors' => $config['vendors'],
		'vendors_constructor' => $config['vendors_constructor'],
	]));


}
catch (Exception $e)
{
	error_log($e->getMessage());
}