<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Command;

use EB\PlantUMLBundle\Drawer\ValidatorDrawer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DrawValidatorCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DrawValidatorCommand extends AbstractPlantUmlCommand
{
    /**
     * @var ValidatorDrawer
     */
    private $validatorDrawer;

    /**
     * @param ValidatorDrawer $validatorDrawer
     */
    public function __construct(ValidatorDrawer $validatorDrawer)
    {
        $this->validatorDrawer = $validatorDrawer;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('eb:uml:validator')
            ->setDescription('Draw entity validation')
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

        return $this->validatorDrawer->draw($file, $format) ? 0 : 1;
    }
}
