<?php declare(strict_types=1);

namespace EB\PlantUMLBundle\Command;

use EB\PlantUMLBundle\Drawer\TwigDrawer;
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
     * @var TwigDrawer
     */
    private $twigDrawer;

    /**
     * @param TwigDrawer $twigDrawer
     */
    public function __construct(TwigDrawer $twigDrawer)
    {
        $this->twigDrawer = $twigDrawer;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
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
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $file = $this->extractFile($input)) {
            $output->writeln('<error>Cannot open target file.</error>');
        }

        $format = $this->extractFormat($input);

        return $this->twigDrawer->draw(
            $file,
            $format,
            $input->getOption('includes'),
            $input->getOption('excludes')
        ) ? 0 : 1;
    }
}
