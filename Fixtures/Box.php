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
     * @var null|string
     */
    private $description;

    /**
     * @var string[]
     */
    private $plaintext = [];

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
     * @var string[]
     */
    private $oneToOne = [];

    /**
     * @var string[]
     */
    private $manyToOne = [];

    /**
     * @var string[]
     */
    private $oneToMany = [];

    /**
     * @var string[]
     */
    private $manyToMany = [];

    /**
     * @var array
     */
    private $visibilityChars = [
        self::VISIBILITY_PRIVATE => '-',
        self::VISIBILITY_PROTECTED => '#',
        self::VISIBILITY_PUBLIC => '+',
    ];

    /**
     * @param string      $name        Name
     * @param null|string $description Description
     */
    public function __construct($name, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param string $text Text
     */
    public function addPlainText($text)
    {
        $this->plaintext[] = $text;
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
     * @param string $manyToOne
     */
    public function addManyToOne($manyToOne)
    {
        $this->manyToOne[] = $manyToOne;
    }

    /**
     * @param string $oneToMany
     */
    public function addOneToMany($oneToMany)
    {
        $this->oneToMany[] = $oneToMany;
    }

    /**
     * @param string $manyToMany
     */
    public function addManyToMany($manyToMany)
    {
        $this->manyToMany[] = $manyToMany;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        if ($this->description) {
            $arr[] = sprintf('class "%s" << %s >>', $this->name, $this->description);
        } else {
            $arr[] = sprintf('class "%s"', $this->name);
        }
        foreach ($this->plaintext as $text) {
            $arr[] = $text;
        }
        foreach ($this->text as list($text, $tab)) {
            $arr[] = sprintf('"%s" : %s%s', $this->name, str_repeat('\t', $tab), $text);
        }
        foreach ($this->parameters as list($name, $type, $visibility)) {
            $arr[] = sprintf('"%s" : %s%s << %s >>', $this->name, $this->visibilityChars[$visibility], $name, $type);
        }
        foreach ($this->extends as $extend) {
            $arr[] = sprintf('"%s" --> "%s"', $this->name, $extend);
        }
        foreach ($this->oneToOne as $oneToOne) {
            $arr[] = sprintf('"%s" --- "%s"', $this->name, $oneToOne);
        }
        foreach ($this->manyToOne as $manyToOne) {
            $arr[] = sprintf('"%s" o-- "%s"', $this->name, $manyToOne);
        }
        foreach ($this->oneToMany as $oneToMany) {
            $arr[] = sprintf('"%s" --o "%s"', $this->name, $oneToMany);
        }
        foreach ($this->manyToMany as $manyToMany) {
            $arr[] = sprintf('"%s" o-o "%s"', $this->name, $manyToMany);
        }

        return $arr;
    }
}
