<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

/**
 * Class DoctrineDrawer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DoctrineDrawer
{
    /**
     * @var PlantUML
     */
    private $plantUML;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param PlantUML      $plantUML PlantUML
     * @param EntityManager $em       Manager
     */
    public function __construct(PlantUML $plantUML, EntityManager $em)
    {
        $this->plantUML = $plantUML;
        $this->em = $em;
    }

    /**
     * @param string $target
     *
     * @return bool
     */
    public function draw($target)
    {
        /** @var ClassMetadata[] $mds */
        $mds = $this->em->getMetadataFactory()->getAllMetadata();

        // Create graph
        $g = [];
        $g[] = '@startuml';
        $g[] = 'set namespaceSeparator none';
        foreach ($mds as $m) {
            $ref = $m->getReflectionClass();

            // Define this entity
            $g[] = sprintf('class %s', $ref->getShortName());

            // Extension
            if (false !== $parent = $ref->getParentClass()) {
                $g[] = sprintf('%s --|> %s', $ref->getShortName(), $parent->getShortName());
            }

            // Properties
            foreach ($ref->getProperties() as $property) {
                if ($m->isSingleValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $g[] = sprintf('%s o-- %s', $ref->getShortName(), $matches[1]);
                    }
                } elseif ($m->isAssociationInverseSide($property->getName())) {
                    // multiple
                } elseif ($m->isCollectionValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $g[] = sprintf('%s o-o %s', $ref->getShortName(), $matches[1]);
                    }
                } else {
                    $g[] = sprintf(
                        '%s : %s%s << %s >>',
                        $ref->getShortName(),
                        $property->isPrivate() ? '-' : ($property->isProtected() ? '#' : '+'),
                        $property->getName(),
                        $m->getTypeOfField($property->getName())
                    );
                }
            }
        }
        $g[] = '@enduml';

        return $this->plantUML->dump($g, $target);
    }
}
