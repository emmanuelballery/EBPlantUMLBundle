<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Command;

use EB\PlantUMLBundle\Drawer\DoctrineDrawer;
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
     * @var DoctrineDrawer
     */
    private $doctrineDrawer;

    /**
     * @param DoctrineDrawer $doctrineDrawer
     */
    public function __construct(DoctrineDrawer $doctrineDrawer)
    {
        $this->doctrineDrawer = $doctrineDrawer;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('eb:uml:doctrine')
            ->setDescription('Draw entity inheritance tree')
            ->addArgument('file', InputArgument::OPTIONAL, 'Target file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $file = $this->extractFile($input)) {
            $output->writeln('<error>Cannot open target file.</error>');
        }

        $format = $this->extractFormat($input);

        return $this->doctrineDrawer->draw($file, $format) ? 0 : 1;
    }
}
