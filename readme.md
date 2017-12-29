Hexgen Command & Handler generator
===============
This is a library to generate command and handlers for an hexagonal architecture approach

### Requirements

- PHP >= v7.0

### Install it with composer

- Execute composer `php composer.phar install`.

- Create your local config `cp config/base.php config/local.php`

####Options
  - `command_namespace` (**Required**) Namespace for the created commands
  - `handler_namespace` (**Required**)Namespace for the created handlers
  - `command_interface_namespace` (**Required**) Namespace of the command interface (needed)
  - `handler_interface_namespace` (**Required**) Namespace of the handler interface (needed)
  - `output` (**Required**) Folder to put your beautiful generated files
  - `dependencies` This is an array used to map your existing services using any PSR container with aliases support.

### Example of usage

If you want to create a new command and handler, just run the index script with some options like:

#####CreateGoose command & handler
`php src/index.php --name=Goose\\CreateGoose --arguments=name,dob --services=database,guzzle,jwt`

**Notice** how the script is using the services to inject those into the handlers like:

***Injected services***
`use mef\Sql\Driver\SqlDriver;`
`use GuzzleHttp\Client;`
`use Firebase\JWT\JWT;`

This dependencies are defined in the config file:

	'dependencies' => [
		'guzzle' => 'GuzzleHttp\\Client',
		'database' => 'mef\\Sql\\Driver\\SqlDriver',
		'jwt' => 'Firebase\\JWT\\JWT',
	],

This will create the **Goose** folder inside `output/commands` and `output/handlers` folders, and here will be placed the new command and handler.

Output

- Command `output/commands/Goose/CreateGooseCommand.php`
- Handler `output/commands/Goose/CreateGooseHandler.php`