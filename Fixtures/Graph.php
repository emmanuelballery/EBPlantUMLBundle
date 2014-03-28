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
     * @param string $name Box name
     *
     * @return Box
     */
    public function addBox($name)
    {
        return $this->boxes[] = new Box($name);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr[] = '@startuml';
        $arr[] = 'set namespaceSeparator none';
        foreach ($this->boxes as $box) {
            $arr = array_merge($arr, $box->toArray());
        }
        $arr[] = '@enduml';

        return $arr;
    }
}
