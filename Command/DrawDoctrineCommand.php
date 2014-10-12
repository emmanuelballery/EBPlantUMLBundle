<?php

namespace EB\PlantUMLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawDoctrineCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawDoctrineCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getContainer()->has('eb.plant_uml_bundle.drawer.doctrine_drawer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('eb:uml:doctrine')
            ->setDescription('Draw entity inheritance tree')
            ->addArgument('file', InputArgument::REQUIRED, 'Target PNG file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->debug($this->getDescription());
        $target = $input->getArgument('file');

        return $this->getContainer()->get('eb.plant_uml_bundle.drawer.doctrine_drawer')->draw($target) ? 0 : 1;
    }
}
