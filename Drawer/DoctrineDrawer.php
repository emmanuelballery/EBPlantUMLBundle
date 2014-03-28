<?php

namespace EB\PlantUMLBundle\Drawer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param PlantUML        $plantUML PlantUML
     * @param KernelInterface $kernel   Kernel
     * @param Filesystem      $fs       FS
     * @param EntityManager   $em       Manager
     */
    public function __construct(PlantUML $plantUML, KernelInterface $kernel, Filesystem $fs, EntityManager $em)
    {
        $this->plantUML = $plantUML;
        $this->kernel = $kernel;
        $this->fs = $fs;
        $this->em = $em;
    }

    /**
     * @param string $target
     *
     * @return bool
     */
    public function draw($target)
    {
        /** @var \ReflectionClass[] $entities */
        $entities = [];

        // List bundles
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            // Find entity path
            $path = $bundle->getPath() . '/Entity';
            if ($this->fs->exists($path)) {
                /** @var SplFileInfo[] $bundleFiles */
                $bundleFiles = Finder::create()->files()->in($path)->depth('<5');
                foreach ($bundleFiles as $file) {
                    if (false !== strpos($file->getFilename(), '~')) {
                        continue;
                    }

                    $namespace = sprintf(
                        '%s\\Entity\\%s',
                        $bundle->getNamespace(),
                        str_replace('/', '\\', mb_strcut($file->getRelativePathname(), 0, mb_strpos($file->getRelativePathname(), '.')))
                    );

                    // @todo Fatal error on traits ...
                    $entities[] = new \ReflectionClass($namespace);
                }
            }
        }

        // Create graph
        $mf = $this->em->getMetadataFactory();
        $g = [];
        $g[] = '@startuml';
        $g[] = 'set namespaceSeparator none';
        foreach ($entities as $entity) {
            if ($entity->isInterface() || $entity->isAbstract() || $entity->isTrait()) {
                continue;
            }

            try {
                $m = $mf->getMetadataFor($entity->getName());
            } catch (\Exception $e) {
                continue;
            }

            // Class
            $g[] = sprintf(
                'class %s',
                $entity->getShortName()
            );

            // Extends ?
            $parent = $entity->getParentClass();
            if ($parent) {
                $g[] = sprintf(
                    '%s --|> %s',
                    $entity->getShortName(),
                    $parent->getShortName()
                );
            }

            // Properties
            foreach ($entity->getProperties() as $property) {
                if ($m->isSingleValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $g[] = sprintf(
                            '%s o-- %s',
                            $entity->getShortName(),
                            $matches[1]
                        );
                    }
                } elseif ($m->isAssociationInverseSide($property->getName())) {
                    // multiple
                } elseif ($m->isCollectionValuedAssociation($property->getName())) {
                    $matches = [];
                    if (preg_match('/targetEntity="([^"]+)"/i', $property->getDocComment(), $matches)) {
                        $g[] = sprintf(
                            '%s o-o %s',
                            $entity->getShortName(),
                            $matches[1]
                        );
                    }
                } else {
                    $g[] = sprintf(
                        '%s : %s%s << %s >>',
                        $entity->getShortName(),
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
