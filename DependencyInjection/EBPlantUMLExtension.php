<?php declare(strict_types=1);

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
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter('eb.plant_uml_bundle.java', null);

        // Require java (sudo apt-get install default-jdk)
        $whichJava = Process::fromShellCommandline('which "java"');
        $whichJava->run();
        if ($whichJava->isSuccessful()) {
            // Require Graphviz software (sudo apt-get install graphviz)
            $whichDot = Process::fromShellCommandline('which "dot"');
            $whichDot->run();
            if ($whichDot->isSuccessful()) {
                $container->setParameter('eb.plant_uml_bundle.java', trim($whichJava->getOutput()));
            }
        }

        // Seems to be fine, load services now
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('drawer.yaml');
    }
}
