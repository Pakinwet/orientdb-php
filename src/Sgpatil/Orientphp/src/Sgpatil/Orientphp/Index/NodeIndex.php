<?php
namespace Sgpatil\Orientphp\Index;

use Sgpatil\Orientphp\Client,
	Sgpatil\Orientphp\Index;

/**
 * Represents a node index in the database
 */
class NodeIndex extends Index
{
	/**
	 * Initialize the index
	 *
	 * @param Client $client
	 * @param string $name
	 * @param array  $config
	 */
	public function __construct(Client $client, $name, $config=array())
	{
		parent::__construct($client, Index::TypeNode, $name, $config);
	}
}
