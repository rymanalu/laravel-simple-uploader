<?php

namespace Rymanalu\LaravelSimpleUploader\Contracts;

interface Factory
{
    /**
     * Specify where the file is provided.
     *
     * @param  string|null  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Uploader
     */
    public function from($provider = null);
}
