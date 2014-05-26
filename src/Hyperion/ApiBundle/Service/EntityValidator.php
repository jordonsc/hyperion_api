<?php
namespace Hyperion\ApiBundle\Service;

class EntityValidator
{
    /**
     * @var array
     */
    protected $mappings;

    function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Check if an entity name is valid
     *
     * @param string $entity
     * @return bool
     */
    public function isValid($entity)
    {
        return array_key_exists($entity, $this->mappings);
    }

    /**
     * Get foreign access_key relationships for this entity
     *
     * @param string $entity
     * @return array
     * @throws \Exception
     */
    public function getForeignKeys($entity)
    {
        if (!array_key_exists($entity, $this->mappings) || !array_key_exists('fk', $this->mappings[$entity])) {
            throw new \Exception("Foreign key data not present in mappings");
        }

        return $this->mappings[$entity]['fk'];
    }

    /**
     * Get a list of searchable fields for an entity
     *
     * @param string $entity
     * @return string[]
     * @throws \Exception
     */
    public function getSearchableFields($entity)
    {
        if (!array_key_exists($entity, $this->mappings) || !array_key_exists('searchable', $this->mappings[$entity])) {
            throw new \Exception("Searchable data not present in mappings");
        }

        return $this->mappings[$entity]['searchable'];
    }

    /**
     * Check if a field is permitted for searching
     *
     * @param string $entity
     * @param string $field
     * @return bool
     */
    public function isSearchable($entity, $field) {
        $searchable = $this->getSearchableFields($entity);
        return in_array($field, $searchable);
    }

}
