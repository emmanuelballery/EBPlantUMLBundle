<?php

namespace EB\PlantUMLBundle\Drawer;

use EB\PlantUMLBundle\Fixtures\Graph;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class PlantUML
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PlantUML
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $java;

    /**
     * @param Filesystem $fs   Filesystem
     * @param string     $java Java path
     */
    public function __construct(Filesystem $fs, $java)
    {
        $this->fs = $fs;
        $this->java = $java;
    }

    /**
     * Dump array data
     *
     * @param Graph  $graph Graph
     * @param string $file  File
     *
     * @return bool
     */
    public function dump(Graph $graph, $file)
    {
        try {
            $isTxt = 'txt' === pathinfo($file, PATHINFO_EXTENSION);

            if ($isTxt) {
                $this->fs->dumpFile($file, implode(PHP_EOL, $graph->toArray()));

                return true;
            }

            $prefix = sys_get_temp_dir() . '/' . uniqid();
            $txt = $prefix . '.txt';
            $png = $prefix . '.png';

            $this->fs->dumpFile($txt, implode(PHP_EOL, $graph->toArray()));

            $plantUml = new Process(sprintf(
                '%s -jar "%s" "%s"',
                $this->java,
                __DIR__ . '/../Resources/lib/plantuml.8008.jar',
                $txt
            ));
            $plantUml->run();

            $this->fs->remove([$txt, $file]);
            $this->fs->rename($png, $file);

            return $plantUml->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
