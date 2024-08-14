<?php

namespace Drahak\Restful;

use ArrayAccess;
use ArrayIterator;
use Exception;
use IteratorAggregate;
use Nette;
use Nette\MemberAccessException;
use Nette\Utils\Json;
use Serializable;

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
    use Nette\SmartObject {
        Nette\SmartObject::__get as SO__get;
        Nette\SmartObject::__set as SO__set;
        Nette\SmartObject::__isset as SO__isset;
        Nette\SmartObject::__unset as SO__unset;
    }

    public function __construct(private array $data = [])
    {
    }

    /**
     * get info if the resource has some data set or is empty
     * @return boolean
     */
    public function hasData()
    {
        return !empty($this->data);
    }

    /**
     * Serialize result set
     * @return string
     */
    public function serialize()
    {
        return Json::encode($this->data);
    }

    /******************** Serializable ********************/

    /**
     * Unserialize Resource
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->data = Json::decode($serialized);
    }

    /**
     * @return bool
     */
    public function offsetExists(mixed $offset)
    {
        return isset($this->data[$offset]);
    }

    /******************** ArrayAccess interface ********************/
    /**
     * @return mixed
     */
    public function offsetGet(mixed $offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        if ($offset === NULL) {
            $offset = count($this->data);
        }
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Get resource data iterator
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getData());
    }

    /******************** Iterator aggregate interface ********************/

    /**
     * Get result set data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /******************** Magic methods ********************/

    /**
     * Magic getter from $this->data
     * @param string $name
     *
     * @return mixed
     * @throws Exception|MemberAccessException
     */
    public function &__get($name)
    {
        try {
            return $this->SO__get($name);
        } catch (MemberAccessException $e) {
            if (isset($this->data[$name])) {
                return $this->data[$name];
            }
            throw $e;
        }

    }

    /**
     * Magic setter to $this->data
     * @param string $name
     */
    public function __set($name, mixed $value)
    {
        try {
            $this->SO__set($name, $value);
        } catch (MemberAccessException) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Magic isset to $this->data
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return !$this->SO__isset($name) ? isset($this->data[$name]) : TRUE;
    }

    /**
     * Magic unset from $this->data
     * @param string $name
     * @throws Exception|MemberAccessException
     */
    public function __unset($name)
    {
        try {
            $this->SO__unset($name);
        } catch (MemberAccessException $e) {
            if (isset($this->data[$name])) {
                unset($this->data[$name]);
                return;
            }
            throw $e;
        }
    }


}
