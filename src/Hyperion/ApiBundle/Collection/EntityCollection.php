<?php

namespace Hyperion\ApiBundle\Collection;

use Doctrine\Common\Inflector\Inflector;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\ApiBundle\Exception\UnexpectedValueException;

class EntityCollection implements \IteratorAggregate
{
    protected $items;

    function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Get the array iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }


    /**
     * Get an entity by ID
     *
     * @param int $key
     * @return object
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function getById($key) {
        return $this->getBy($key, 'id');
    }


    /**
     * Get an entity by any given field
     *
     * @param mixed $key
     * @param string $field
     * @return object
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function getBy($key, $field)
    {
        foreach ($this->items as $item) {
            $fn = 'get'.Inflector::classify($field);
            if ($item->$fn() == $key) {
                return $item;
            }
        }

        throw new NotFoundException("Entity ".$field." not found in collection: ".$key);
    }

    /**
     * Returns the number of projects in the collection
     *
     * @return int
     */
    public function count() {
        return count($this->items);
    }

    /**
     * @return object
     */
    public function current() {
        return $this->getIterator()->current();
    }

}
