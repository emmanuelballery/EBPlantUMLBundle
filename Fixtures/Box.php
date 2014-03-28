<?php

namespace EB\PlantUMLBundle\Fixtures;

/**
 * Class Box
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Box
{
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PRIVATE = 2;
    const VISIBILITY_PROTECTED = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $text = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $extends = [];

    /**
     * @var array
     */
    private $oneToOne = [];

    /**
     * @var array
     */
    private $oneToMany = [];

    /**
     * @var array
     */
    private $visibilityChars = [
        self::VISIBILITY_PRIVATE => '-',
        self::VISIBILITY_PROTECTED => '#',
        self::VISIBILITY_PUBLIC => '+',
    ];

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $text Text
     * @param int    $tab  Tab count
     */
    public function addText($text, $tab = 0)
    {
        $this->text[] = [$text, $tab];
    }

    /**
     * @param string $name       Parameter name
     * @param string $type       Parameter type
     * @param int    $visibility Parameter visibility
     */
    public function addParameter($name, $type = 'integer', $visibility = self::VISIBILITY_PRIVATE)
    {
        $this->parameters[] = [$name, $type, $visibility];
    }

    /**
     * @param string $extends
     */
    public function addExtends($extends)
    {
        $this->extends[] = $extends;
    }

    /**
     * @param string $oneToOne
     */
    public function addOneToOne($oneToOne)
    {
        $this->oneToOne[] = $oneToOne;
    }

    /**
     * @param string $oneToMany
     */
    public function addOneToMany($oneToMany)
    {
        $this->oneToMany[] = $oneToMany;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr[] = sprintf('class %s', $this->name);
        foreach ($this->text as $text) {
            list($text, $tab) = $text;
            $arr[] = sprintf('"%s": "%s%s"', $this->name, str_repeat('\t', $tab), $text);
        }
        foreach ($this->parameters as $parameter) {
            list($name, $type, $visibility) = $parameter;
            $arr[] = sprintf('"%s" : %s%s << %s >>', $this->name, $this->visibilityChars[$visibility], $name, $type);
        }
        foreach ($this->extends as $extend) {
            $arr[] = sprintf('"%s" --> "%s"', $this->name, $extend);
        }
        foreach ($this->oneToOne as $oneToOne) {
            $arr[] = sprintf('"%s" o-- "%s"', $this->name, $oneToOne);
        }
        foreach ($this->oneToMany as $oneToMany) {
            $arr[] = sprintf('"%s" o-o "%s"', $this->name, $oneToMany);
        }

        return $arr;
    }
}
