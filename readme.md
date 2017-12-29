Hexgen Command & Handler generator
===============
This is a library to generate command and handlers for an hexagonal architecture approach

### Requirements

- PHP >= v7.0

### Install it with composer

- Execute composer `php composer.phar install`.

- Create your local config `cp config/base.php config/local.php`

### Options
  - `command_namespace` (**Required**) Namespace for the created commands
  - `handler_namespace` (**Required**)Namespace for the created handlers
  - `command_interface_namespace` (**Required**) Namespace of the command interface (needed)
  - `handler_interface_namespace` (**Required**) Namespace of the handler interface (needed)
  - `output` (**Required**) Folder to put your beautiful generated files
  - `dependencies` This is an array used to map your existing services using any PSR container with aliases support.

### Example of usage

If you want to create a new command and handler, just run the index script with some options like:

**CreateGoose command & handler**
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

**Command**

    <?php namespace Goose\App\Command\Goose;
    
    use Goose\App\Command\CommandInterface;
    
    /**
     * Command for CreateGoose
     */
    class CreateGooseCommand implements CommandInterface
    {
    	/**
    	* @var string Name
    	*/
    	protected $name;
    
    	/**
    	* @var string Dob
    	*/
    	protected $dob;
    
    	/**
    	 * Create the command
    	 *
    	 * @param string $name
    	 * @param string $dob
    	 */
    	public function __construct(string $name, string $dob)
    	{
    		$this->name = $name;
    		$this->dob = $dob;
    	}
    
    	/**
    	 * @return string
    	 */
    	public function getName() : string
    	{
    		return $this->name;
    	}
    	/**
    	 * @return string
    	 */
    	public function getDob() : string
    	{
    		return $this->dob;
    	}
    }


**Handler**

    <?php namespace Goose\App\Handler\Goose;
    
    use Goose\App\Handler\HandlerInterface;
    use Goose\App\Command\Goose\CreateGooseCommand;
    use mef\Sql\Driver\SqlDriver;
    use GuzzleHttp\Client;
    use Firebase\JWT\JWT;
    
    /**
     * Handle the CreateGoose command
     */
    class CreateGooseHandler implements HandlerInterface
    {
    	/**
    	* @var \mef\Sql\Driver\SqlDriver
    	*/
    	protected $database;
    
    	/**
    	* @var \GuzzleHttp\Client
    	*/
    	protected $guzzle;
    
    	/**
    	* @var \Firebase\JWT\JWT
    	*/
    	protected $jwt;
    
    	/**
    	 * Create the command
    	 *
    	 * @param \mef\Sql\Driver\SqlDriver $database
    	 * @param \GuzzleHttp\Client $guzzle
    	 * @param \Firebase\JWT\JWT $jwt
    	 */
    	public function __construct(SqlDriver $database, Client $guzzle, JWT $jwt)
    	{
    		$this->database = $database;
    		$this->guzzle = $guzzle;
    		$this->jwt = $jwt;
    	}
    
    	/**
    	 * Handle command
    	 */
    	public function handle(CreateGooseCommand $command)
    	{
    		return [];
    	}
    }

Output

- Command `output/commands/Goose/CreateGooseCommand.php`
- Handler `output/commands/Goose/CreateGooseHandler.php`


### TODO

 1. Decouple the script in **beautiful** classes
 2. Allow to the user define the type of data for the command arguments
 3. Add aliases for service names
 4. Add a new `--crud` option to generate a new **CRUD** for any new context, like: `CreateThing, UpdateThing, DeleteThing, SearchThing`.