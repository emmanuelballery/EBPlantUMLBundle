<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param PlantUML               $plantUML PlantUML
     * @param EntityManagerInterface $em       Manager
     */
    public function __construct(PlantUML $plantUML, EntityManagerInterface $em)
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
                $visibility = Box::VISIBILITY_PRIVATE;
                if (false !== $property = $this->findProperty($ref, $field)) {
                    if ($property->isProtected()) {
                        $visibility = Box::VISIBILITY_PROTECTED;
                    } elseif ($property->isPublic()) {
                        $visibility = Box::VISIBILITY_PUBLIC;
                    }
                }

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

    /**
     * Find property
     *
     * @param \ReflectionClass $ref   Class
     * @param string           $field Field
     *
     * @return \ReflectionProperty|bool
     */
    private function findProperty(\ReflectionClass $ref, $field)
    {
        while (false !== $ref && !$ref->hasProperty($field)) {
            $ref = $ref->getParentClass();
        }

        if (false === $ref) {
            return false;
        }

        return $ref->getProperty($field);
    }
}
