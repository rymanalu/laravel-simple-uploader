<?php

namespace Rymanalu\LaravelSimpleUploader\Providers;

use Rymanalu\LaravelSimpleUploader\Contracts\Provider;
use Rymanalu\LaravelSimpleUploader\Support\FileSetter;
use Rymanalu\LaravelSimpleUploader\Support\FileExtensionPathInfo;

class UrlProvider implements Provider
{
    use FileExtensionPathInfo, FileSetter;

    /**
     * Returns whether the url is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        $fileHeaders = @get_headers($this->file);

        return $fileHeaders && $fileHeaders[0] !== 'HTTP/1.1 404 Not Found';
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
