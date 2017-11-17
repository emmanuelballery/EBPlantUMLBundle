<?php

namespace EB\PlantUMLBundle\Drawer;

use EB\PlantUMLBundle\Fixtures\Graph;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class PlantUML
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PlantUML
{
    const FORMAT_TXT = 'txt';
    const FORMAT_PNG = 'png';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var null|string
     */
    private $java;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Filesystem           $fs     Filesystem
     * @param null|string          $java   Java path
     * @param null|LoggerInterface $logger Logger
     */
    public function __construct(Filesystem $fs, $java = null, LoggerInterface $logger = null)
    {
        $this->fs = $fs;
        $this->java = $java;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Dump array data
     *
     * @param Graph    $graph  Graph
     * @param resource $file   File
     * @param string   $format Plant UML Format
     *
     * @return bool
     */
    public function dump(Graph $graph, $file, $format = PlantUML::FORMAT_TXT)
    {
        $format = $format ?: self::FORMAT_TXT;

        try {
            if (self::FORMAT_TXT === $format) {
                if (false !== fwrite($file, implode(PHP_EOL, $graph->toArray()) . PHP_EOL)) {
                    return fclose($file);
                }

                return false;
            }

            if (self::FORMAT_PNG === $format) {
                if (null === $this->java) {
                    return false;
                }

                $prefix = sys_get_temp_dir() . '/' . uniqid();
                $txtPath = $prefix . '.txt';
                $pngPath = $prefix . '.png';

                $clean = function () use ($txtPath, $pngPath) {
                    $this->fs->remove([$txtPath, $pngPath]);
                };

                $this->fs->dumpFile(
                    $txtPath,
                    implode(PHP_EOL, $graph->toArray())
                );

                $plantUml = new Process(sprintf(
                    '%s -jar "%s" "%s"',
                    $this->java,
                    __DIR__ . '/../Resources/lib/plantuml.1.2017.19.jar',
                    $txtPath
                ));
                $plantUml->run();

                if ($plantUml->isSuccessful()) {
                    if (false !== $png = fopen($pngPath, 'r')) {
                        if (0 !== stream_copy_to_stream($png, $file)) {
                            $clean();

                            return fclose($file);
                        }
                    }
                } else {
                    $this->logger->error($plantUml->getErrorOutput());
                }

                $clean();

                return false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
