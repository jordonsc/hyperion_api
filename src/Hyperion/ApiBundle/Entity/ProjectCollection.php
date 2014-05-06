<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Inflector\Inflector;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\ApiBundle\Exception\UnexpectedValueException;

class ProjectCollection implements \IteratorAggregate
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
     * Get a Project by ID
     *
     * @param int $key
     * @return Project
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function getById($key) {
        return $this->getBy($key, 'id');
    }

    /**
     * Get a Project by name
     *
     * @param string $key
     * @return Project
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function getByName($key) {
        return $this->getBy($key, 'name');
    }

    /**
     * Get an item by any given field
     *
     * @param mixed $key
     * @param string $field
     * @return Project
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    protected function getBy($key, $field)
    {
        foreach ($this->items as $item) {
            if (!($item instanceof Project)) {
                throw new UnexpectedValueException("Unexpected entity type: ".get_class($item));
            }

            $fn = 'get'.Inflector::classify($field);
            if ($item->$fn() == $key) {
                return $item;
            }
        }

        throw new NotFoundException("Project ".$field." not found in collection: ".$key);
    }

    /**
     * Returns the number of projects in the collection
     *
     * @return int
     */
    public function count() {
        return count($this->items);
    }
}