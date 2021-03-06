<?php

namespace Rymanalu\LaravelSimpleUploader\Providers;

use Rymanalu\LaravelSimpleUploader\Contracts\Provider;
use Rymanalu\LaravelSimpleUploader\Support\FileSetter;
use Rymanalu\LaravelSimpleUploader\Support\FileExtensionPathInfo;

class LocalProvider implements Provider
{
    use FileExtensionPathInfo, FileSetter;

    /**
     * Returns whether the file is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return file_exists($this->file);
    }

    /**
     * Get the file's contents.
     *
     * @return resource|string
     */
    public function getContents()
    {
        return fopen($this->file, 'r');
    }
}
