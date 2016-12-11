<?php

namespace Rymanalu\LaravelSimpleUploader\Support;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rymanalu\LaravelSimpleUploader\UploaderManager
 * @see \Rymanalu\LaravelSimpleUploader\Uploader
 */
class Uploader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'uploader';
    }
}
