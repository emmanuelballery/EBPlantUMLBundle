<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
     * Draw
     *
     * @param resource $target Target file
     * @param string   $format Plant UML format
     *
     * @return bool
     */
    public function draw($target, $format)
    {
        /** @var ClassMetadata[] $mds */
        $mds = $this->em->getMetadataFactory()->getAllMetadata();

        // Create graph
        $g = new Graph();

        // Create all boxes
        foreach ($mds as $m) {
            $g->addBox($m->getReflectionClass()->getName());
        }

        // Add all data
        foreach ($mds as $m) {
            $ref = $m->getReflectionClass();

            // Define this entity
            $box = $g->getBox($ref->getName());

            // Class extension
            if (false !== $parent = $ref->getParentClass()) {
                $box->addExtends($parent->getName());
            }

            // Fields
            foreach ($m->getFieldNames() as $field) {
                $property = $ref->getProperty($field);
                $visibility = $property->isPrivate() ? Box::VISIBILITY_PRIVATE : ($property->isProtected() ? Box::VISIBILITY_PROTECTED : Box::VISIBILITY_PUBLIC);
                $isNullable = $m->isNullable($field);
                $box->addParameter($field, $m->getTypeOfField($field), $visibility, !$isNullable);
            }

            // Associations
            foreach ($m->getAssociationNames() as $field) {
                $mapping = $m->getAssociationMapping($field);
                switch ($mapping['type']) {
                    case ClassMetadataInfo::MANY_TO_MANY:
                        if ($mapping['isOwningSide']) {
                            $box->addManyToMany(
                                $mapping['targetEntity'],
                                $mapping['fieldName']
                            );
                        }
                        break;
                    case ClassMetadataInfo::MANY_TO_ONE:
                        if ($mapping['isOwningSide']) {
                            $box->addManyToOne(
                                $mapping['targetEntity'],
                                $mapping['fieldName']
                            );
                        }
                        break;
                    case ClassMetadataInfo::ONE_TO_MANY:
                        if ($mapping['isOwningSide']) {
                            $box->addOneToMany(
                                $mapping['targetEntity'],
                                $mapping['fieldName']
                            );
                        }
                        break;
                    case ClassMetadataInfo::ONE_TO_ONE:
                        if ($mapping['isOwningSide']) {
                            $box->addOneToOne(
                                $mapping['targetEntity'],
                                $mapping['fieldName']
                            );
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        return $this->plantUML->dump($g, $target, $format);
    }
}
