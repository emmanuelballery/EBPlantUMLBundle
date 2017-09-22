<?php

namespace EB\PlantUMLBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawDoctrineCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawDoctrineCommand extends AbstractPlantUmlCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this
            ->getContainer()
            ->has('eb.plant_uml_bundle.drawer.doctrine_drawer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('eb:uml:doctrine')
            ->setDescription('Draw entity inheritance tree')
            ->addArgument('file', InputArgument::OPTIONAL, 'Target file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $file = $this->extractFile($input)) {
            $output->writeln('<error>Cannot open target file.</error>');
        }

        $format = $this->extractFormat($input);

        return $this
            ->getContainer()
            ->get('eb.plant_uml_bundle.drawer.doctrine_drawer')
            ->draw($file, $format) ? 0 : 1;
    }
}
