<?php

namespace EB\PlantUMLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Process\Process;
use Symfony\Component\Config\FileLocator;

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
        // Require java (sudo apt-get install default-jdk)
        $whichJava = new Process('which "java"');
        $whichJava->run();
        if (true === $whichJava->isSuccessful()) {
            // Require Graphviz software (sudo apt-get install graphviz)
            $whichDot = new Process('which "dot"');
            $whichDot->run();
            if (true === $whichDot->isSuccessful()) {
                $container->setParameter('eb.plant_uml_bundle.java', trim($whichJava->getOutput()));

                // All seems to be fine, load services now
                $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
                $loader->load('services.xml');
            }
        }
    }
}
