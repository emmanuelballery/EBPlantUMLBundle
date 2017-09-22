<?php

namespace EB\PlantUMLBundle\Drawer;

use EB\PlantUMLBundle\Fixtures\Graph;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TwigDrawer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TwigDrawer
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var PlantUML
     */
    private $plantUML;

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var array
     */
    private $resolvedFiles = [];

    /**
     * @var array
     */
    private $excludes = [];

    /**
     * @var array
     */
    private $includes = [];

    /**
     * @param PlantUML        $plantUML Plant UML
     * @param KernelInterface $kernel   Kernel
     * @param Filesystem      $fs       Filesystem
     */
    public function __construct(PlantUML $plantUML, KernelInterface $kernel, Filesystem $fs)
    {
        $this->kernel = $kernel;
        $this->plantUML = $plantUML;
        $this->fs = $fs;
    }

    /**
     * @param resource $target   Target
     * @param string   $format   Plant UML format
     * @param array    $includes Includes files
     * @param array    $excludes Excluded files
     *
     * @return bool
     */
    public function draw($target, $format = PlantUML::FORMAT_TXT, array $includes = [], array $excludes = [])
    {
        $this->files = [];
        $this->resolvedFiles = [];
        $this->excludes = $excludes;
        $this->includes = $includes;

        // List views
        $path = $this->kernel->getRootDir() . '/Resources/views';
        if ($this->fs->exists($path)) {
            /** @var SplFileInfo[] $appFiles */
            $appFiles = Finder::create()->files()->in($path)->depth('<5');
            foreach ($appFiles as $file) {
                $this->load($file);
            }
        }
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            $path = $bundle->getPath() . '/Resources/views';
            if ($this->fs->exists($path)) {
                /** @var SplFileInfo[] $bundleFiles */
                $bundleFiles = Finder::create()->files()->in($path)->depth('<5');
                foreach ($bundleFiles as $file) {
                    $this->load($file, $bundle);
                }
            }
        }
        array_map([$this, 'resolve'], $this->files);

        // Create graph
        $g = new Graph();
        foreach ($this->resolvedFiles as $id => $file) {
            $box = $g->addBox($id);

            // Defined blocks
            if (0 !== count($file['defined_blocks']) || 0 !== count($file['called_blocks'])) {
                $box->addText('blocks');
                if (0 !== count($file['defined_blocks'])) {
                    $box->addText('defined', 1);
                    foreach ($file['defined_blocks'] as $block) {
                        $box->addText($block, 2);
                    }
                }
                if (0 !== count($file['called_blocks'])) {
                    $box->addText('called', 1);
                    foreach ($file['called_blocks'] as $block) {
                        $box->addText($block, 2);
                    }
                }
            }

            // Extends
            foreach ($file['extends'] as $extend) {
                $box->addExtends($extend['id']);
            }

            // Traits
            foreach ($file['uses'] as $use) {
                $box->addText(sprintf('use "%s"', $use['id']));
                if (!empty($use['defined_blocks'])) {
                    $box->addText('defined', 1);
                    foreach ($use['defined_blocks'] as $block) {
                        $box->addText($block, 2);
                    }
                }
                if (!empty($use['called_blocks'])) {
                    $box->addText('called', 1);
                    foreach ($use['called_blocks'] as $block) {
                        $box->addText($block, 2);
                    }
                }
            }

            // Includes
            foreach ($file['includes'] as $include) {
                $box->addText(sprintf('include "%s"', $include['id']));
            }
        }

        return $this->plantUML->dump($g, $target, $format);
    }

    /**
     * Load
     *
     * @param SplFileInfo $file   File
     * @param null|Bundle $bundle Bundle
     */
    private function load(SplFileInfo $file, Bundle $bundle = null)
    {
        $id = null;

        if (null === $bundle) {
            if (false !== $appResourcesViewsPath = realpath($this->kernel->getRootDir() . '/Resources/views')) {
                if (0 === mb_strpos($file->getRealPath(), $appResourcesViewsPath)) {
                    $id = mb_strcut($file->getRealPath(), 1 + mb_strlen($appResourcesViewsPath));
                }
            }
        } else {
            if (false !== $bundleResourcesViewsPath = realpath($bundle->getPath() . '/Resources/views')) {
                if (0 === mb_strpos($file->getRealPath(), $bundleResourcesViewsPath)) {
                    $id = '@' . mb_strcut($bundle->getName(), 0, -6);
                    $id .= mb_strcut($file->getRealPath(), mb_strlen($bundleResourcesViewsPath));
                }
            }
        }

        if (null !== $id) {
            $this->files[$id] = [
                'id' => $id,
                'resolved' => false,
                'path' => $file->getRealPath(),
                'file' => $file->getBasename(),
                'bundle_name' => $bundle ? $bundle->getName() : null,
                'extends' => [],
                'uses' => [],
                'includes' => [],
                'defined_blocks' => [],
                'called_blocks' => [],
            ];
        }
    }

    /**
     * @param array &$file
     */
    private function resolve(array &$file)
    {
        if ($this->isAllowed($file['path'])) {
            $this->resolvedFiles[$file['id']] = &$file;
        }
        if ($file['resolved']) {
            return;
        }

        // Analyse
        $content = file_get_contents($file['path']);

        // Extends
        $extends = [];
        if (preg_match('/extends\s+\'([^\']+)\'/i', $content, $extends)) {
            if (array_key_exists($extends[1], $this->files)) {
                $file['extends'][] = &$this->files[$extends[1]];
                $this->resolve($this->files[$extends[1]]);
            }
        }
        unset($extends);
        $extends = [];
        if (preg_match('/extends[^\?]+\?\s*\'([^\']+)\'\s*:\s*\'([^\']+)\'/i', $content, $extends)) {
            if (array_key_exists($extends[1], $this->files)) {
                $file['extends'][] = &$this->files[$extends[1]];
                $this->resolve($this->files[$extends[1]]);
            }
            if (array_key_exists($extends[2], $this->files)) {
                $file['extends'][] = &$this->files[$extends[2]];
                $this->resolve($this->files[$extends[2]]);
            }
        }
        unset($extends);

        // Uses
        $uses = [];
        if (preg_match_all('/use\s+\'([^\']+)\'/i', $content, $uses)) {
            sort($uses[1]);
            foreach ($uses[1] as $use) {
                if (array_key_exists($use, $this->files)) {
                    $file['uses'][] = &$this->files[$use];
                    $this->resolve($this->files[$use]);
                }
            }
        }
        unset($uses);

        // Include
        $includePatterns = [
            '/include\(\'([^\']+)\'/i',
            '/include\("([^"]+)"/i',
            '/include\s+\'([^\']+)\'/i',
            '/include\s+"([^"]+)"/i',
        ];
        foreach ($includePatterns as $includePattern) {
            $includes = [];
            if (preg_match_all($includePattern, $content, $includes)) {
                $includes[1] = array_unique($includes[1]);
                sort($includes[1]);
                foreach ($includes[1] as $include) {
                    if (isset($this->files[$include])) {
                        $file['includes'][] = &$this->files[$include];
                        $this->resolve($this->files[$include]);
                    }
                }
            }
            unset($includes);
        }

        // Block
        $blocks = [];
        if (preg_match_all('/block\s+([a-z_]+)/i', $content, $blocks)) {
            $blocks[1] = array_unique($blocks[1]);
            sort($blocks[1]);
            foreach ($blocks[1] as $block) {
                $file['defined_blocks'][] = $block;
            }
        }
        $blocks = [];
        if (preg_match_all('/block\(\'([a-z_]+)\'\)/i', $content, $blocks)) {
            $blocks[1] = array_unique($blocks[1]);
            sort($blocks[1]);
            foreach ($blocks[1] as $block) {
                $file['called_blocks'][] = $block;
            }
        }
        unset($blocks);

        $file['resolved'] = true;
    }

    /**
     * @param string $template
     *
     * @return bool
     */
    private function isAllowed($template)
    {
        foreach ($this->excludes as $exclude) {
            if (true === $this->match($template, $exclude)) {
                return false;
            }
        }
        foreach ($this->includes as $include) {
            if (true === $this->match($template, $include)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $template Template
     * @param string $pattern  Pattern
     *
     * @return bool
     */
    private function match($template, $pattern)
    {
        if (empty($pattern)) {
            return null;
        }

        return false !== strpos($template, $pattern);
    }
}
