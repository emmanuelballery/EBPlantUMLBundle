<?php

namespace EB\PlantUMLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawTwigCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawTwigCommand extends ContainerAwareCommand
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
            ->addArgument('file', InputArgument::REQUIRED, 'Target PNG file')
            ->addOption('includes', 'i', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Includes', [])
            ->addOption('excludes', 'x', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Excludes', []);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this
            ->getContainer()
            ->get('eb.plant_uml_bundle.drawer.twig_drawer')
            ->draw($input->getArgument('file'), $input->getOption('includes'), $input->getOption('excludes')) ? 0 : 1;
    }
}
