<?php

namespace Ptuchik\CoreUtilities\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage as BaseStorage;
use League\Flysystem\Adapter\Local;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

/**
 * Class Storage
 * @package Ptuchik\CoreUtilities\Helpers
 */
class Storage
{
    /**
     * Disk instance
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $disk;

    /**
     * Visbility
     * @var string
     */
    protected $visibility;

    /**
     * Path prefix to be prepended
     * @var \Illuminate\Config\Repository|mixed|string
     */
    protected $prefix = '';

    /**
     * Storage constructor.
     *
     * @param bool $public
     */
    public function __construct($public = false)
    {
        // Get disk name from our configuration
        $disk = $public ? config('ptuchik-core-utilities.disks.public') : config('ptuchik-core-utilities.disks.private');

        // Get disk from native filesystems configuration
        $disk = config('filesystems.disks.'.$disk) ? $disk : BaseStorage::getDefaultDriver();

        // Set prefix
        if ($prefix = config('filesystems.disks.'.$disk.'.path_prefix')) {
            $this->prefix = '/'.$prefix;
        }

        // Get disk instance
        $this->disk = BaseStorage::disk($disk);

        // Set visibility
        $this->visibility = $public ? Filesystem::VISIBILITY_PUBLIC : $this->visibility = Filesystem::VISIBILITY_PRIVATE;
        $this->disk->getDriver()->getConfig()->set('disable_asserts', true);
    }

    /**
     * Get a file system instance
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getDisk() : Filesystem
    {
        return $this->disk;
    }

    /**
     * Get file or folder name from path
     *
     * @param $path
     *
     * @return string
     */
    public function name($path) : string
    {
        return array_last(explode(DIRECTORY_SEPARATOR, $path));
    }

    /**
     * Get the contents of a file.
     *
     * @param  string $path
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path) : string
    {
        // If disk is not local
        if (!($this->disk->getDriver()->getAdapter() instanceof Local)) {

            // Fix for some cloud storage provider's caching of public files
            if ($this->visibility == Filesystem::VISIBILITY_PUBLIC) {
                return file_get_contents($this->disk->url($path).'?rand='.time());
            }
        }

        return $this->getDisk()->get($path);
    }

    /**
     * Get public URL
     *
     * @param $path
     *
     * @return string
     */
    public function url($path) : string
    {
        return $this->disk->url($path);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string $directory
     *
     * @return array
     */
    public function directories($directory = null)
    {
        return $this->getDisk()->directories($directory);
    }

    /**
     * Get all (recursive) of the directories within a given directory.
     *
     * @param  string|null $directory
     *
     * @return array
     */
    public function allDirectories($directory = null) : array
    {
        return $this->getDisk()->allDirectories($directory);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null $directory
     * @param  bool        $recursive
     *
     * @return array
     */
    public function files($directory = null, $recursive = false)
    {
        return $this->getDisk()->files($directory, $recursive);
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null $directory
     *
     * @return array
     */
    public function allFiles($directory = null)
    {
        return $this->getDisk()->allFiles($directory);
    }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param        $content
     * @param null   $visibility
     *
     * @return bool
     */
    public function put(string $path, $content, $visibility = null) : bool
    {
        return $this->getDisk()->put($path, $content, $visibility ?: $this->visibility);
    }

    /**
     * Append to file.
     *
     * @param string          $path
     * @param string|resource $content
     *
     * @return bool
     */
    public function append(string $path, $content) : bool
    {
        return $this->getDisk()->append($path, $content);
    }

    /**
     * Determine if a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path) : bool
    {
        return $this->getDisk()->exists($path);
    }

    /**
     * Copy a file to a new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function copy(string $from, string $to) : bool
    {
        return $this->getDisk()->copy($from, $to);
    }

    /**
     * Copy directory to a new location
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function copyDirectory(string $from, string $to)
    {
        $files = $this->allFiles($from);
        foreach ($files as $file) {
            if (!$this->copy($file, str_replace_first($from, $to, $file))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Move a file to a new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function move(string $from, string $to) : bool
    {
        return $this->getDisk()->move($from, $to);
    }

    /**
     * Move directory to a new location
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function moveDirectory(string $from, string $to)
    {
        $disk = $this->getDisk();

        $files = $this->allFiles($from);
        foreach ($files as $file) {
            if (!$this->move($file, str_replace_first($from, $to, $file))) {
                return false;
            }
        }

        if ($disk->getDriver()->getAdapter() instanceof Local) {
            return $disk->deleteDirectory($from);
        }

        return true;
    }

    /**
     * Make directory
     *
     * @param      $path
     * @param int  $mode
     * @param bool $recursive
     * @param bool $force
     *
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false) : bool
    {
        return $this->getDisk()->makeDirectory($path, $mode, $recursive, $force);
    }

    /**
     * Remove a directory and all of its files
     *
     * @param string $directory
     *
     * @return bool
     */
    public function deleteDirectory(string $directory) : bool
    {
        $disk = $this->getDisk();

        if ($disk->getDriver()->getAdapter() instanceof Local) {
            return $disk->deleteDirectory($directory);
        }

        return $disk->delete($this->allFiles($directory));
    }

    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     *
     * @return bool
     */
    public function delete($paths) : bool
    {
        if (is_array($paths)) {
            $absolutePath = array_map(function ($path) {
                return $path;
            }, $paths);
        } else {
            $absolutePath = $paths;
        }

        return $this->getDisk()->delete($absolutePath);
    }
}