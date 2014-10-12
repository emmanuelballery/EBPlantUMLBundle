<?php

namespace EB\PlantUMLBundle\Fixtures;

/**
 * Class Graph
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Graph
{
    /**
     * @var Box[]
     */
    private $boxes = [];

    /**
     * Create a new Box
     *
     * @param string      $name        Box name
     * @param null|string $description Description
     *
     * @return Box
     */
    public function addBox($name, $description = null)
    {
        return $this->boxes[$name] = new Box($name, $description);
    }

    /**
     * Get box
     *
     * @param string $name
     *
     * @return null|Box
     */
    public function getBox($name)
    {
        return array_key_exists($name, $this->boxes) ? $this->boxes[$name] : null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = ['@startuml', 'set namespaceSeparator none'];
        foreach ($this->boxes as $box) {
            $arr = array_merge($arr, $box->toArray());
        }
        $arr[] = '@enduml';

        return $arr;
    }
}
