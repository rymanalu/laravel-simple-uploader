<?php

namespace Rymanalu\LaravelSimpleUploader\Contracts;

use Closure;

interface Factory
{
    /**
     * Register a custom file provider Closure.
     *
     * @param  string  $provider
     * @param  \Closure  $callback
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Factory
     * @throws \InvalidArgumentException
     */
    public function extend($provider, Closure $callback);

    /**
     * Specify where the file is provided.
     *
     * @param  string|null  $provider
     * @return \Rymanalu\LaravelSimpleUploader\Contracts\Uploader
     */
    public function from($provider = null);
}
