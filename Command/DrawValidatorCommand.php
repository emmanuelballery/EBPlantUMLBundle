<?php

namespace EB\PlantUMLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawValidatorCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawValidatorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getContainer()->has('eb.plant_uml_bundle.drawer.validator_drawer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('eb:uml:validator')
            ->setDescription('Draw entity validation')
            ->addArgument('file', InputArgument::REQUIRED, 'Target PNG file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this
            ->getContainer()
            ->get('eb.plant_uml_bundle.drawer.validator_drawer')
            ->draw($input->getArgument('file')) ? 0 : 1;
    }
}
