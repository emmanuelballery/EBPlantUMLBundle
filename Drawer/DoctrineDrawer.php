<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use EB\PlantUMLBundle\Fixtures\Box;
use EB\PlantUMLBundle\Fixtures\Graph;

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
        $g = new Graph();
        foreach ($mds as $m) {
            $ref = $m->getReflectionClass();

            // Define this entity
            $box = $g->addBox($ref->getShortName());

            // Extension
            if (false !== $parent = $ref->getParentClass()) {
                $box->addExtends($parent->getShortName());
            }

            // Properties
            foreach ($ref->getProperties() as $property) {
                if ($m->isSingleValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $box->addOneToMany($matches[1]);
                    }
                } elseif ($m->isAssociationInverseSide($property->getName())) {
                    // multiple
                } elseif ($m->isCollectionValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $box->addOneToOne($matches[1]);
                    }
                } else {
                    $visibility = $property->isPrivate() ? Box::VISIBILITY_PRIVATE : ($property->isProtected() ? Box::VISIBILITY_PROTECTED : Box::VISIBILITY_PUBLIC);
                    $box->addParameter($property->getName(), $m->getTypeOfField($property->getName()), $visibility);
                }
            }
        }

        return $this->plantUML->dump($g, $target);
    }
}
