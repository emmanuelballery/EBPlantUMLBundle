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
     * @param bool   $isRequired Is required
     */
    public function addParameter($name, $type = 'integer', $visibility = self::VISIBILITY_PRIVATE, $isRequired = false)
    {
        $this->parameters[] = [$name, $type, $visibility, $isRequired];
    }

    /**
     * @param string $extends
     */
    public function addExtends($extends)
    {
        $this->extends[] = $extends;
    }

    /**
     * @param string      $class     Class
     * @param null|string $fieldName Field name
     */
    public function addOneToOne($class, $fieldName = null)
    {
        $this->oneToOne[] = [$class, $fieldName];
    }

    /**
     * @param string      $class     Class
     * @param null|string $fieldName Field name
     */
    public function addManyToOne($class, $fieldName = null)
    {
        $this->manyToOne[] = [$class, $fieldName];
    }

    /**
     * @param string      $class     Class
     * @param null|string $fieldName Field name
     */
    public function addOneToMany($class, $fieldName = null)
    {
        $this->oneToMany[] = [$class, $fieldName];
    }

    /**
     * @param string      $class     Class
     * @param null|string $fieldName Field name
     */
    public function addManyToMany($class, $fieldName = null)
    {
        $this->manyToMany[] = [$class, $fieldName];
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
        foreach ($this->parameters as list($name, $type, $visibility, $isRequired)) {
            $arr[] = sprintf('"%s" : %s%s << %s%s >>', $this->name, $this->visibilityChars[$visibility], $name, $isRequired ? '' : '?', $type);
        }
        foreach ($this->extends as $extend) {
            $arr[] = sprintf('"%s" --> "%s"', $this->name, $extend);
        }
        foreach ($this->oneToOne as list($class, $fieldName)) {
            if ($fieldName) {
                $arr[] = sprintf('"%s" --- "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                $arr[] = sprintf('"%s" --- "%s"', $this->name, $class);
            }
        }
        foreach ($this->manyToOne as list($class, $fieldName)) {
            if ($fieldName) {
                $arr[] = sprintf('"%s" o-- "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                $arr[] = sprintf('"%s" o-- "%s"', $this->name, $class);
            }
        }
        foreach ($this->oneToMany as list($class, $fieldName)) {
            if ($fieldName) {
                $arr[] = sprintf('"%s" --o "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                $arr[] = sprintf('"%s" --o "%s"', $this->name, $class);
            }
        }
        foreach ($this->manyToMany as list($class, $fieldName)) {
            if ($fieldName) {
                $arr[] = sprintf('"%s" o-o "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                $arr[] = sprintf('"%s" o-o "%s"', $this->name, $class);
            }
        }

        return $arr;
    }
}
