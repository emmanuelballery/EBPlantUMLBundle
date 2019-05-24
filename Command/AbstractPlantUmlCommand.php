<?php

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
     * Extract file
     *
     * @param InputInterface $input
     *
     * @return null|resource
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
     * Extract format
     *
     * @param InputInterface $input
     *
     * @return null|string
     */
    protected function extractFormat(InputInterface $input)
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

        return $format;
    }
}
