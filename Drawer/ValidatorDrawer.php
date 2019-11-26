<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\EntityManagerInterface;
use EB\PlantUMLBundle\Fixtures\Graph;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param PlantUML               $plantUML  Plant UML
     * @param EntityManagerInterface $em        Entity Manager
     * @param ValidatorInterface     $validator Validator
     */
    public function __construct(PlantUML $plantUML, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->plantUML = $plantUML;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * Draw
     *
     * @param resource $target Target
     * @param string   $format Plant UML format
     *
     * @return bool
     */
    public function draw($target, string $format = PlantUML::FORMAT_TXT): bool
    {
        /** @var ClassMetadata[] $mds */
        $mds = $this->em->getMetadataFactory()->getAllMetadata();

        // Create graph
        $g = new Graph();
        foreach ($mds as $m) {
            $ref = $m->getReflectionClass();
            $mdt = $this->validator->getMetadataFor($ref->getName());
            if ($mdt instanceof ClassMetadata) {
                $box = $g->addBox($ref->getShortName());
                $box->addPlainText(sprintf('class "%s" {', $ref->getShortName()));

                // Properties
                foreach ($mdt->getConstrainedProperties() as $property) {
                    $box->addPlainText(sprintf('.. %s ..', $property));
                    $metadata = $mdt->getPropertyMetadata($property);
                    if (is_array($metadata)) {
                        $metadata = array_shift($metadata);
                    }
                    if ($metadata instanceof PropertyMetadata) {
                        foreach ($metadata->getConstraints() as $constraint) {
                            if ($constraint instanceof Assert\Type) {
                                $box->addPlainText(sprintf('Type("%s")', $constraint->type));
                            } elseif ($constraint instanceof Assert\Length) {
                                if ($constraint->min) {
                                    $box->addPlainText(sprintf('Length(min = %u)', $constraint->min));
                                }
                                if ($constraint->max) {
                                    $box->addPlainText(sprintf('Length(max = %u)', $constraint->max));
                                }
                            } else {
                                $names = explode('\\', get_class($constraint));
                                $box->addPlainText(sprintf('%s()', array_pop($names)));
                            }
                        }
                    }
                }
                $box->addPlainText('}');
            }
        }

        return $this->plantUML->dump($g, $target, $format);
    }
}
