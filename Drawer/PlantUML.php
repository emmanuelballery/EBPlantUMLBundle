<?php

namespace EB\PlantUMLBundle\Drawer;

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
     * @param array  $data Data
     * @param string $file File
     *
     * @return bool
     */
    public function dump(array $data, $file)
    {
        $prefix = sys_get_temp_dir() . '/' . uniqid();
        $txt = $prefix . '.txt';
        $png = $prefix . '.png';

        $this->fs->dumpFile($txt, implode(PHP_EOL, $data));
        $plantUml = new Process(sprintf(
            '%s -jar "%s" "%s"',
            $this->java,
            __DIR__ . '/../Resources/lib/plantuml.jar',
            $txt
        ));
        $plantUml->run();
        $this->fs->remove($txt);
        $this->fs->remove($file);
        $this->fs->rename($png, $file);

        return $plantUml->isSuccessful();
    }
}
