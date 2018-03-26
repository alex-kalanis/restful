<?php
namespace Drahak\Restful;

use ArrayAccess;
use Serializable;
use ArrayIterator;
use IteratorAggregate;
use Nette;
use Nette\Utils\Json;
use Nette\MemberAccessException;

/**
 * REST resource
 * @package Drahak\Restful
 * @author Drahomír Hanák
 *
 * @property string $contentType Allowed result content type
 * @property-read array $data
 */
class Resource implements ArrayAccess, Serializable, IteratorAggregate, IResource
{
	use Nette\SmartObject;

	/** @var array */
	private $data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		$this->data = $data;
	}

	/**
	 * Get result set data
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * get info if the resource has some data set or is empty
	 * @return boolean
	 */
	public function hasData()
	{
		return !empty($this->data);
	}

	/******************** Serializable ********************/

	/**
	 * Serialize result set
	 * @return string
	 */
	public function serialize()
	{
		return Json::encode($this->data);
	}

	/**
	 * Unserialize Resource
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		$this->data = Json::decode($serialized);
	}

	/******************** ArrayAccess interface ********************/

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->data[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if ($offset === NULL) {
			$offset = count($this->data);
		}
		$this->data[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}

	/******************** Iterator aggregate interface ********************/

	/**
	 * Get resource data iterator
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getData());
	}
}
