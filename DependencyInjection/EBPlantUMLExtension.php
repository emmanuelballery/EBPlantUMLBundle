<?php

namespace EB\PlantUMLBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Process\Process;

/**
 * Class EBPlantUMLExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EBPlantUMLExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->setParameter('eb.plant_uml_bundle.java', null);

        // Require java (sudo apt-get install default-jdk)
        $whichJava = new Process('which "java"');
        $whichJava->run();
        if ($whichJava->isSuccessful()) {
            // Require Graphviz software (sudo apt-get install graphviz)
            $whichDot = new Process('which "dot"');
            $whichDot->run();
            if ($whichDot->isSuccessful()) {
                $container->setParameter('eb.plant_uml_bundle.java', trim($whichJava->getOutput()));
            }
        }

        // All seems to be fine, load services now
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('drawer.yml');
    }
}
