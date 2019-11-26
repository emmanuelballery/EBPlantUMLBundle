<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Fixtures;

use Generator;

/**
 * Class Box
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Box
{
    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 2;
    public const VISIBILITY_PROTECTED = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
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
     * @var string[]
     */
    private $visibilityChars = [
        self::VISIBILITY_PRIVATE => '-',
        self::VISIBILITY_PROTECTED => '#',
        self::VISIBILITY_PUBLIC => '+',
    ];

    /**
     * @param string      $name        Name
     * @param string|null $description Description
     */
    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Add Plain Text
     *
     * @param string $text Text
     */
    public function addPlainText(string $text): void
    {
        $this->plaintext[] = $text;
    }

    /**
     * Add Text
     *
     * @param string $text Text
     * @param int    $tab  Tab count
     */
    public function addText(string $text, int $tab = 0): void
    {
        $this->text[] = [$text, $tab];
    }

    /**
     * Add Parameter
     *
     * @param string $name         Parameter Name
     * @param string $type         Parameter Type
     * @param int    $visibility   Parameter Visibility
     * @param bool   $isRequired   Is Required
     * @param bool   $isIdentifier Is Identifier
     * @param bool   $isUnique     Is Unique
     */
    public function addParameter(
        string $name,
        string $type = 'integer',
        int $visibility = self::VISIBILITY_PRIVATE,
        bool $isRequired = false,
        bool $isIdentifier = false,
        bool $isUnique = false
    ): void {
        $this->parameters[] = [$name, $type, $visibility, $isRequired, $isIdentifier, $isUnique];
    }

    /**
     * Add Extends
     *
     * @param string $extends
     */
    public function addExtends(string $extends): void
    {
        $this->extends[] = $extends;
    }

    /**
     * Add One To One
     *
     * @param string      $class     Class
     * @param string|null $fieldName Field name
     */
    public function addOneToOne(string $class, ?string $fieldName = null): void
    {
        $this->oneToOne[] = [$class, $fieldName];
    }

    /**
     * Add Many To One
     *
     * @param string      $class     Class
     * @param string|null $fieldName Field name
     */
    public function addManyToOne(string $class, ?string $fieldName = null): void
    {
        $this->manyToOne[] = [$class, $fieldName];
    }

    /**
     * Add One To Many
     *
     * @param string      $class     Class
     * @param string|null $fieldName Field name
     */
    public function addOneToMany(string $class, ?string $fieldName = null): void
    {
        $this->oneToMany[] = [$class, $fieldName];
    }

    /**
     * Add Many To Many
     *
     * @param string      $class     Class
     * @param string|null $fieldName Field name
     */
    public function addManyToMany(string $class, ?string $fieldName = null): void
    {
        $this->manyToMany[] = [$class, $fieldName];
    }

    /**
     * To Array
     *
     * @return string[]|Generator
     */
    public function toArray(): Generator
    {
        if ($this->description) {
            yield sprintf('class "%s" << %s >>', $this->name, $this->description);
        } else {
            yield sprintf('class "%s"', $this->name);
        }
        foreach ($this->plaintext as $text) {
            yield $text;
        }
        foreach ($this->text as list($text, $tab)) {
            yield sprintf('"%s" : %s%s', $this->name, str_repeat('\t', $tab), $text);
        }
        foreach ($this->parameters as list($name, $type, $visibility, $isRequired, $isIdentifier, $isUnique)) {
            yield sprintf(
                '"%s" : %s%s « %s%s%s%s »',
                $this->name,
                $this->visibilityChars[$visibility],
                $name,
                $isRequired ? '' : '?',
                $type,
                $isIdentifier ? ' - identifier' : '',
                $isUnique ? ' - unique' : ''
            );
        }
        foreach ($this->extends as $extend) {
            yield sprintf('"%s" --> "%s"', $this->name, $extend);
        }
        foreach ($this->oneToOne as list($class, $fieldName)) {
            if ($fieldName) {
                yield sprintf('"%s" --- "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                yield sprintf('"%s" --- "%s"', $this->name, $class);
            }
        }
        foreach ($this->manyToOne as list($class, $fieldName)) {
            if ($fieldName) {
                yield sprintf('"%s" o-- "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                yield sprintf('"%s" o-- "%s"', $this->name, $class);
            }
        }
        foreach ($this->oneToMany as list($class, $fieldName)) {
            if ($fieldName) {
                yield sprintf('"%s" --o "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                yield sprintf('"%s" --o "%s"', $this->name, $class);
            }
        }
        foreach ($this->manyToMany as list($class, $fieldName)) {
            if ($fieldName) {
                yield sprintf('"%s" o-o "%s" : "%s"', $this->name, $class, $fieldName);
            } else {
                yield sprintf('"%s" o-o "%s"', $this->name, $class);
            }
        }
    }
}
