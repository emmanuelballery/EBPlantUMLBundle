<?php

namespace EB\PlantUMLBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawTwigCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawTwigCommand extends AbstractPlantUmlCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getContainer()->has('eb.plant_uml_bundle.drawer.twig_drawer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('eb:uml:twig')
            ->setDescription('Draw twig inheritance tree')
            ->addArgument('file', InputArgument::OPTIONAL, 'Target file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format')
            ->addOption('includes', 'i', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Includes', [])
            ->addOption('excludes', 'x', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Excludes', []);
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
            ->get('eb.plant_uml_bundle.drawer.twig_drawer')
            ->draw($file, $format, $input->getOption('includes'), $input->getOption('excludes')) ? 0 : 1;
    }
}
