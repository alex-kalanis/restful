<?php

namespace kalanis\Restful;


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
 * @package kalanis\Restful
 *
 * @property string $contentType Allowed result content type
 * @property array<string|int, mixed> $data
 * @template TK of string|int
 * @template TVal of mixed
 * @implements ArrayAccess<TK, TVal>
 * @implements IteratorAggregate<ArrayIterator<TK, TVal>>
 */
class Resource implements ArrayAccess, Serializable, IteratorAggregate, IResource
{
    use Nette\SmartObject {
        Nette\SmartObject::__get as SO__get;
        Nette\SmartObject::__set as SO__set;
        Nette\SmartObject::__isset as SO__isset;
        Nette\SmartObject::__unset as SO__unset;
    }

    /**
     * @param array<string|int, mixed> $data
     */
    public function __construct(
        private array $data = [],
    )
    {
    }

    /**
     * get info if the resource has some data set or is empty
     */
    public function hasData(): bool
    {
        return !empty($this->data);
    }

    /******************** Serializable ********************/

    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * @param array<string|int, mixed> $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Serialize result set
     */
    public function serialize(): string
    {
        return Json::encode($this->data);
    }

    /**
     * Unserialize Resource
     */
    public function unserialize(string $data): void
    {
        $this->data = (array) Json::decode($data, true);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /******************** ArrayAccess interface ********************/

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[strval($offset)];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $offset = count($this->data);
        }
        $this->data[strval($offset)] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[strval($offset)]);
    }

    /**
     * Get resource data iterator
     * @return ArrayIterator<string|int, mixed>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getData());
    }

    /******************** Iterator aggregate interface ********************/

    /**
     * Get result set data
     * @return array<string|int, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /******************** Magic methods ********************/

    /**
     * Magic getter from $this->data
     * @param string $name
     *
     * @throws Exception|MemberAccessException
     * @return mixed
     */
    public function &__get(string $name)
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
     */
    public function __set(string $name, mixed $value)
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
    public function __isset(string $name)
    {
        return !$this->SO__isset($name) ? isset($this->data[$name]) : true;
    }

    /**
     * Magic unset from $this->data
     * @param string $name
     * @throws Exception|MemberAccessException
     */
    public function __unset(string $name)
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
