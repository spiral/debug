<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Commands\Spiral;

use Spiral\Console\Command;
use Spiral\Core\BootloadProcessor;
use Spiral\Core\Core;
use Spiral\Core\DirectoriesInterface;
use Spiral\Files\FilesInterface;

/**
 * Reload application boot-loading list.
 */
class ReloadCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'app:reload';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Reload application boot-loading list.';

    /**
     * @param FilesInterface       $files
     * @param DirectoriesInterface $directories
     */
    public function perform(FilesInterface $files, DirectoriesInterface $directories)
    {
        $cacheDirectory = $directories->directory('cache');

        if (!$files->exists($cacheDirectory)) {
            $this->writeln("Cache directory is missing, no cache to be cleaned.");

            return;
        }

        $files->delete($cacheDirectory . BootloadProcessor::MEMORY . Core::EXTENSION);
        $this->writeln("<info>Bootload cache has been cleared.</info>");
    }
}