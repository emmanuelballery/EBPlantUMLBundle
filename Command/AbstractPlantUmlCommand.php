<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Command;

use EB\PlantUMLBundle\Drawer\PlantUML;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class AbstractPlantUmlCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
abstract class AbstractPlantUmlCommand extends Command
{
    /**
     * Extract File
     *
     * @param InputInterface $input
     *
     * @return resource|null
     */
    protected function extractFile(InputInterface $input)
    {
        $resource = STDOUT;
        if (null !== $file = $input->getArgument('file')) {
            if (false === $resource = fopen($file, 'w')) {
                return null;
            }
        }

        return $resource;
    }

    /**
     * Extract Format
     *
     * @param InputInterface $input
     *
     * @return string|null
     */
    protected function extractFormat(InputInterface $input): ?string
    {
        if (null === $format = $input->getOption('format')) {
            if (null !== $file = $input->getArgument('file')) {
                $ext = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ('txt' === $ext) {
                    return PlantUML::FORMAT_TXT;
                }
                if ('uml' === $ext) {
                    return PlantUML::FORMAT_UML;
                }
                if ('png' === $ext) {
                    return PlantUML::FORMAT_PNG;
                }
                if ('svg' === $ext) {
                    return PlantUML::FORMAT_SVG;
                }
                if ('atxt' === $ext) {
                    return PlantUML::FORMAT_ATXT;
                }
                if ('utxt' === $ext) {
                    return PlantUML::FORMAT_UTXT;
                }
            }
        }

        return PlantUML::FORMAT_SVG;
    }
}
