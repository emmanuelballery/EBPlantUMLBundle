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
        $this->getContainer()->get('logger')->debug($this->getDescription());

        $target = $input->getArgument('file');

        if (false === $this->getContainer()->has('eb.plant_uml_bundle.drawer.validator_drawer')) {
            $output->write('<error>Validator drawer missing. Please check your requirements.</error>');

            return 0;
        }

        return $this->getContainer()->get('eb.plant_uml_bundle.drawer.validator_drawer')->draw($target) ? 0 : 1;
    }
}
