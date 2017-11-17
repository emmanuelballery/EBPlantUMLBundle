<?php

namespace EB\PlantUMLBundle\Drawer;

use EB\PlantUMLBundle\Fixtures\Graph;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class PlantUML
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PlantUML
{
    const FORMAT_TXT = 'txt';
    const FORMAT_PNG = 'png';
    const FORMAT_SVG = 'svg';
    const FORMAT_UML = 'uml';
    const FORMAT_ATXT = 'atxt';
    const FORMAT_UTXT = 'utxt';

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
            $content = implode(PHP_EOL, $graph->toArray()) . PHP_EOL;

            if (self::FORMAT_UML === $format) {
                $url = sprintf('http://www.plantuml.com/plantuml/uml/%s', $this->urlEncode($content));

                if (false !== fwrite($file, $url . PHP_EOL)) {
                    return fclose($file);
                }

                return false;
            }

            if (self::FORMAT_TXT === $format) {
                if (false !== fwrite($file, $content)) {
                    return fclose($file);
                }

                return false;
            }

            if (in_array($format, [self::FORMAT_PNG, self::FORMAT_SVG, self::FORMAT_ATXT, self::FORMAT_UTXT], true)) {
                if (null === $this->java) {
                    return false;
                }

                $prefix = sys_get_temp_dir() . '/' . uniqid();
                $txtPath = $prefix . '.txt';
                $pngPath = $prefix . '.' . $format;

                $clean = function () use ($txtPath, $pngPath) {
                    $this->fs->remove([$txtPath, $pngPath]);
                };

                $this->fs->dumpFile($txtPath, $content);

                $builder = new ProcessBuilder();
                $builder
                    ->add($this->java)
                    ->add('-jar')
                    ->add(__DIR__ . '/../Resources/lib/plantuml.1.2017.19.jar')
                    ->add($txtPath);

                if (self::FORMAT_SVG === $format) {
                    $builder->add('-tsvg');
                }

                if (self::FORMAT_ATXT === $format) {
                    $builder->add('-txt');
                }

                if (self::FORMAT_UTXT === $format) {
                    $builder->add('-utxt');
                }

                $plantUml = $builder->getProcess();

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

    /**
     * Url encode
     *
     * @param string $text
     *
     * @return string
     */
    private function urlEncode($text)
    {
        return $this->urlEncode64(gzdeflate(utf8_encode($text), 9));
    }

    /**
     * Url encode64
     *
     * @param string $c
     *
     * @return string
     */
    private function urlEncode64($c)
    {
        $str = '';
        $len = strlen($c);
        for ($i = 0; $i < $len; $i += 3) {
            if ($len === $i + 2) {
                $str .= $this->urlAppend3bytes(
                    ord(substr($c, $i, 1)),
                    ord(substr($c, $i + 1, 1)),
                    0
                );
            } else {
                if ($len === $i + 1) {
                    $str .= $this->urlAppend3bytes(
                        ord(substr($c, $i, 1)),
                        0,
                        0
                    );
                } else {
                    $str .= $this->urlAppend3bytes(
                        ord(substr($c, $i, 1)),
                        ord(substr($c, $i + 1, 1)),
                        ord(substr($c, $i + 2, 1))
                    );
                }
            }
        }

        return $str;
    }

    /**
     * Url append 3bytes
     *
     * @param int $b1
     * @param int $b2
     * @param int $b3
     *
     * @return string
     */
    private function urlAppend3bytes($b1, $b2, $b3)
    {
        $c1 = $b1 >> 2;
        $c2 = (($b1 & 0x3) << 4) | ($b2 >> 4);
        $c3 = (($b2 & 0xF) << 2) | ($b3 >> 6);
        $c4 = $b3 & 0x3F;

        return implode([
            $this->urlEncode6bit($c1 & 0x3F),
            $this->urlEncode6bit($c2 & 0x3F),
            $this->urlEncode6bit($c3 & 0x3F),
            $this->urlEncode6bit($c4 & 0x3F),
        ]);
    }

    /**
     * Url encode 6bit
     *
     * @param int $b
     *
     * @return string
     */
    private function urlEncode6bit($b)
    {
        if ($b < 10) {
            return chr(48 + $b);
        }
        $b -= 10;
        if ($b < 26) {
            return chr(65 + $b);
        }
        $b -= 26;
        if ($b < 26) {
            return chr(97 + $b);
        }
        $b -= 26;
        if ($b == 0) {
            return '-';
        }
        if ($b == 1) {
            return '_';
        }

        return '?';
    }
}
