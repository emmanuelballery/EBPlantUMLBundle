<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Fixtures;

use Generator;

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
     * Create A New Box
     *
     * @param string      $name        Box name
     * @param string|null $description Description
     *
     * @return Box
     */
    public function addBox(string $name, ?string $description = null)
    {
        return $this->boxes[$name] = new Box($name, $description);
    }

    /**
     * Get Box
     *
     * @param string $name
     *
     * @return Box|null
     */
    public function getBox(string $name): ?Box
    {
        return $this->boxes[$name] ?? null;
    }

    /**
     * To Array
     *
     * @return string[]|Generator
     */
    public function toArray(): Generator
    {
        yield '@startuml';
        yield 'set namespaceSeparator none';
        foreach ($this->boxes as $box) {
            yield from $box->toArray();
        }
        yield '@enduml';
    }
}
