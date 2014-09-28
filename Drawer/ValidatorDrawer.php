<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use EB\PlantUMLBundle\Fixtures\Box;
use EB\PlantUMLBundle\Fixtures\Graph;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Class ValidatorDrawer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ValidatorDrawer
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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param PlantUML           $plantUML  Plant UML
     * @param EntityManager      $em        Entity manager
     * @param ValidatorInterface $validator Validator
     */
    public function __construct(PlantUML $plantUML, EntityManager $em, ValidatorInterface $validator)
    {
        $this->plantUML = $plantUML;
        $this->em = $em;
        $this->validator = $validator;
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
